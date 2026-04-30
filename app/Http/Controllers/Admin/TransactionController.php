<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use App\Models\ProductVariant;
use App\Models\Category;
use App\DataTables\TransactionsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Display transaction history.
     */
    public function index(TransactionsDataTable $dataTable)
    {
        // Get summary statistics
        $todayIncomes = TransactionPayment::whereDate('payment_date', today())->sum(DB::raw('`amount` - `change`'));
        $todayTransactions = Transaction::whereDate('created_at', today())->count();
        $monthIncomes = TransactionPayment::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year)
            ->sum(DB::raw('amount - `change`'));
        $totalTransactions = Transaction::count();
        $totalSoldVariants = TransactionItem::sum('quantity');

        return $dataTable->render('admin.transactions.index', compact(
            'todayIncomes',
            'todayTransactions',
            'monthIncomes',
            'totalTransactions',
            'totalSoldVariants'
        ));
    }

    /**
     * Display transaction details.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['items.productVariant.product', 'user', 'payments'])->findOrFail($id);

        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Print transaction receipt.
     */
    public function printReceipt($id)
    {
        $transaction = Transaction::with(['items.productVariant.product', 'user', 'payments'])->findOrFail($id);
        $businessProfile = BusinessProfile::first();

        return view('admin.transactions.print', compact('transaction', 'businessProfile'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit($id)
    {
        $transaction = Transaction::with(['items.productVariant.product', 'customer', 'payments'])->findOrFail($id);
        $categories = Category::active()->orderBy('name')->get();
        
        // Prepare initial cart data
        $initialCart = $transaction->items->map(function($item) {
            return [
                'variant_id' => $item->product_variant_id,
                'product_name' => $item->product_name,
                'variant_name' => $item->variant_name,
                'sku' => $item->productVariant->sku ?? '',
                'price' => (float) $item->price,
                'quantity' => $item->quantity,
                'discount_percent' => ($item->price * $item->quantity) > 0 ? (($item->discount / ($item->price * $item->quantity)) * 100) : 0,
                'cut_amount' => (float) $item->cut_amount,
                'stock' => ($item->productVariant->stock ?? 0) + $item->quantity, // Add current qty back to stock for validation
                'image' => $item->productVariant->product->primary_image_url ?? asset('images/no-image.png'),
            ];
        });

        // Prepare initial payments data
        $initialPayments = $transaction->payments->map(function($payment) {
            return [
                'amount' => (float) $payment->amount,
                'payment_method' => $payment->payment_method,
                'payment_date' => $payment->payment_date->format('Y-m-d'),
                'status' => $payment->status,
                'notes' => $payment->notes,
            ];
        });

        return view('admin.transactions.edit', compact('transaction', 'categories', 'initialCart', 'initialPayments'));
    }

    /**
     * Update the specified transaction in storage.
     */
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
            'items.*.cut_amount' => 'nullable|numeric|min:0',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'grand_total' => 'required|numeric|min:0',
            'payment_method' => 'required_if:payment_mode,full',
            'amount_paid' => 'required_if:payment_mode,full|numeric',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
            'payment_mode' => 'required|in:full,partial',
            'payments' => 'required_if:payment_mode,partial|array',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.payment_method' => 'required|string',
            'payments.*.payment_date' => 'required|date',
            'payments.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();

        try {
            // 1. Revert Stock (Add back original items)
            foreach ($transaction->items as $item) {
                if ($item->productVariant) {
                    $item->productVariant->adjustStock(
                        $item->quantity,
                        'in',
                        auth()->id(),
                        $transaction->invoice_no,
                        'Transaction Edit - Reversal',
                        null
                    );
                }
            }

            // 2. Clear Existing Items
            $transaction->items()->delete();

            // 3. Update Transaction Details
            $transaction->update([
                'customer_id' => $request->customer_id,
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'grand_total' => $request->grand_total,
                'payment_mode' => $request->payment_mode,
                'payment_method' => $request->payment_mode === 'full' ? $request->payment_method : 'multiple',
                'amount_paid' => $request->payment_mode === 'full' ? $request->grand_total : 0, // Will be recalculated from payments
                'notes' => $request->notes,
            ]);

            // 4. Handle Payments (Delete existing and recreate)
            $transaction->payments()->delete();
            
            if ($request->payment_mode === 'full') {
                $transaction->payments()->create([
                    'amount' => $request->amount_paid,
                    'change' => max(0, $request->amount_paid - $request->grand_total),
                    'payment_method' => $request->payment_method,
                    'payment_date' => now(),
                    'status' => $request->amount_paid >= $request->grand_total ? 'paid' : 'partial',
                    'processed_by' => auth()->id(),
                ]);
            } else {
                $totalPayments = count($request->payments);
                foreach ($request->payments as $index => $p) {
                    // Usually installments don't have change, but if they overpay on the last one, 
                    // we can't easily calculate here without total sum.
                    // For simplicity, change during Edit is primarily for Full Payment mode.
                    $transaction->payments()->create([
                        'amount' => $p['amount'],
                        'change' => 0,
                        'payment_method' => $p['payment_method'],
                        'payment_date' => $p['payment_date'],
                        'notes' => $p['notes'] ?? null,
                        'status' => 'paid',
                        'processed_by' => auth()->id(),
                    ]);
                }
            }

            // Refresh status from payments
            $transaction->refreshPaymentStatus();

            // 5. Process New Items
            foreach ($request->items as $item) {
                $variant = ProductVariant::with('product')->find($item['variant_id']);
                
                // Validate stock (considering we just added back the refined stock)
                if ($variant->stock < $item['quantity']) {
                    throw new \Exception("Insufficient stock for {$variant->full_name}. Available: {$variant->stock}");
                }

                // Create transaction item
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_variant_id' => $variant->id,
                    'product_name' => $variant->product->name ?? 'Unknown Product',
                    'variant_name' => $variant->name,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'] ?? 0,
                    'cut_amount' => $item['cut_amount'] ?? 0,
                    'subtotal' => ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0) - ($item['cut_amount'] ?? 0),
                ]);

                // Deduct stock
                $variant->adjustStock(
                    $item['quantity'],
                    'out',
                    auth()->id(),
                    $transaction->invoice_no,
                    'Transaction Edit - Modification'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('transaction.updated'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('transaction.update_failed') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Remove the specified transaction from storage.
     */
    public function destroy($id)
    {
        abort_if(!auth()->user()->can('Delete Transaction'), 403, 'You do not have permission to delete transactions.');

        $transaction = Transaction::with('items.productVariant')->findOrFail($id);

        DB::beginTransaction();

        try {
            // 1. Restore Stock
            foreach ($transaction->items as $item) {
                if ($item->productVariant) {
                    $item->productVariant->adjustStock(
                        $item->quantity,
                        'in',
                        auth()->id(),
                        $transaction->invoice_no,
                        'Transaction Deleted - Stock Restored',
                        null
                    );
                }
            }

            // 2. Delete Transaction (Cascade should handle items, but manual delete is safer for hooks if any)
            $transaction->items()->delete();
            $transaction->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('transaction.deleted'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('transaction.delete_failed') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Add a payment to an existing transaction.
     */
    public function addPayment(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        $remainingBalance = (float) $transaction->outstanding_balance;

        $request->validate([
            'amount'         => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'payment_date'   => 'required|date',
            'notes'          => 'nullable|string|max:1000',
        ]);

        // Gate: deposit usage requires permission
        if ($request->payment_method === 'deposit') {
            abort_if(!auth()->user()->can('Use Deposit'), 403, 'You do not have permission to use deposit.');

            // Must have a linked registered customer
            if (!$transaction->customer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deposit can only be used for transactions with a registered customer.',
                ], 422);
            }

            $customer = Customer::find($transaction->customer_id);
            if ((float) $customer->deposit_balance < (float) $request->amount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient deposit balance. Available: Rp ' . number_format($customer->deposit_balance, 0, ',', '.'),
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            if ($request->payment_method === 'deposit') {
                // Deduct from customer deposit ledger
                $customer = Customer::find($transaction->customer_id);
                $depositEntry = $customer->useDeposit(
                    (float) $request->amount,
                    $transaction->id,
                    auth()->id()
                );

                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'amount'         => $request->amount,
                    'change'         => 0,
                    'payment_method' => 'deposit',
                    'payment_date'   => $request->payment_date,
                    'status'         => 'paid',
                    'notes'          => $request->notes,
                    'deposit_id'     => $depositEntry->id,
                ]);
            } else {
                $change = max(0, $request->amount - $remainingBalance);

                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'amount'         => $request->amount,
                    'change'         => $change,
                    'payment_method' => $request->payment_method,
                    'payment_date'   => $request->payment_date,
                    'status'         => 'paid',
                    'notes'          => $request->notes,
                ]);
            }

            $transaction->refreshPaymentStatus();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('transaction.payment_recorded'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => __('general.something_went_wrong') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the payment date of a transaction payment.
     */
    public function updatePaymentDate(Request $request, $id)
    {
        $payment = TransactionPayment::findOrFail($id);

        $request->validate([
            'payment_date' => 'required|date',
        ]);

        try {
            $payment->update([
                'payment_date' => $request->payment_date,
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Payment date updated successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Failed to update payment date') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
