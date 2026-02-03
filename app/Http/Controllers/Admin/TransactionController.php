<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessProfile;
use App\Models\Transaction;
use App\Models\TransactionItem;
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
        $todaySales = Transaction::whereDate('created_at', today())->sum('grand_total');
        $todayTransactions = Transaction::whereDate('created_at', today())->count();
        $monthSales = Transaction::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('grand_total');
        $totalTransactions = Transaction::count();

        return $dataTable->render('admin.transactions.index', compact(
            'todaySales',
            'todayTransactions',
            'monthSales',
            'totalTransactions'
        ));
    }

    /**
     * Display transaction details.
     */
    public function show($id)
    {
        $transaction = Transaction::with(['items.productVariant.product', 'user'])->findOrFail($id);

        return view('admin.transactions.show', compact('transaction'));
    }

    /**
     * Print transaction receipt.
     */
    public function printReceipt($id)
    {
        $transaction = Transaction::with(['items.productVariant.product', 'user'])->findOrFail($id);
        $businessProfile = BusinessProfile::first();

        return view('admin.transactions.print', compact('transaction', 'businessProfile'));
    }

    /**
     * Show the form for editing the specified transaction.
     */
    public function edit($id)
    {
        $transaction = Transaction::with(['items.productVariant.product', 'customer'])->findOrFail($id);
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
                'stock' => $item->productVariant->stock + $item->quantity, // Add current qty back to stock for validation
                'image' => $item->productVariant->product->primary_image_url ?? asset('images/no-image.png'),
            ];
        });

        return view('admin.transactions.edit', compact('transaction', 'categories', 'initialCart'));
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
            'payment_method' => 'required|in:cash,card,transfer,other',
            'amount_paid' => 'required|numeric|min:0',
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
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
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'grand_total' => $request->grand_total,
                'payment_method' => $request->payment_method,
                'amount_paid' => $request->amount_paid,
                'change' => $request->amount_paid - $request->grand_total,
                'notes' => $request->notes,
                'customer_id' => $request->customer_id,
            ]);

            // 4. Process New Items
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
                'message' => 'Transaction updated successfully!',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update transaction: ' . $e->getMessage(),
            ], 500);
        }
    }
}
