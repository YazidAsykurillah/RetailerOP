<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class POSController extends Controller
{
    /**
     * Display the POS interface.
     */
    public function index()
    {
        $categories = Category::active()->orderBy('name')->get();
        
        // Get featured/popular products for quick access grid
        $featuredProducts = ProductVariant::with(['product.primaryImage', 'product.category'])
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->limit(12)
            ->get();

        return view('admin.pos.index', compact('categories', 'featuredProducts'));
    }

    /**
     * Display the dedicated POS interface (no sidebar).
     */
    public function dedicated()
    {
        return view('admin.pos.dedicated');
    }

    /**
     * Search products/variants for POS.
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');
        $categoryId = $request->get('category_id');

        $query = ProductVariant::with(['product.primaryImage', 'variantValues'])
            ->whereHas('product', function ($q) use ($search) {
                $q->where('is_active', true)
                    ->where(function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            })
            ->where('is_active', true)
            ->where('stock', '>', 0);

        // Filter by category if provided
        if ($categoryId) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // Also search by variant SKU
        $query->orWhere(function ($q) use ($search) {
            $q->where('sku', 'like', "%{$search}%")
                ->where('is_active', true)
                ->where('stock', '>', 0);
        });

        $variants = $query->limit(20)->get()->map(function ($variant) {
            $productName = $variant->product->name ?? 'Unknown Product';
            $variantName = $variant->name ?: 'Default';
            
            return [
                'id' => $variant->id,
                'text' => "{$productName} - {$variantName} (SKU: {$variant->sku})",
                'product_name' => $productName,
                'variant_name' => $variantName,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'stock' => $variant->stock,
                'image' => $variant->product->primary_image_url ?? asset('images/no-image.png'),
            ];
        });

        return response()->json(['results' => $variants]);
    }

    /**
     * Find product by SKU for barcode scanner.
     */
    public function findBySku(Request $request)
    {
        $sku = $request->get('sku');

        if (!$sku) {
            return response()->json(['success' => false, 'message' => __('pos.sku_required')], 400);
        }

        $variant = ProductVariant::with(['product.primaryImage'])
            ->where('sku', $sku)
            ->whereHas('product', function ($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->first();

        if (!$variant) {
            return response()->json(['success' => false, 'message' => __('pos.product_not_found')], 404);
        }

        if ($variant->stock <= 0) {
            return response()->json(['success' => false, 'message' => __('pos.product_out_of_stock')], 400);
        }

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'product_name' => $variant->product->name ?? 'Unknown Product',
                'variant_name' => $variant->name ?: 'Default',
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'stock' => $variant->stock,
                'image' => $variant->product->primary_image_url ?? asset('images/no-image.png'),
            ]
        ]);
    }

    /**
     * Get product variant details.
     */
    public function getProductDetails($id)
    {
        $variant = ProductVariant::with(['product.primaryImage', 'variantValues'])->find($id);

        if (!$variant) {
            return response()->json(['success' => false, 'message' => __('pos.product_not_found')], 404);
        }

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'product_name' => $variant->product->name ?? 'Unknown Product',
                'variant_name' => $variant->name ?: 'Default',
                'full_name' => $variant->full_name,
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'stock' => $variant->stock,
                'image' => $variant->product->primary_image_url ?? asset('images/no-image.png'),
            ],
        ]);
    }

    /**
     * Get products by category for grid display.
     */
    public function getByCategory(Request $request)
    {
        $categoryId = $request->get('category_id');

        $query = ProductVariant::with(['product.primaryImage'])
            ->whereHas('product', function ($q) {
                $q->where('is_active', true);
            })
            ->where('is_active', true)
            ->where('stock', '>', 0);

        if ($categoryId) {
            $query->whereHas('product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        $variants = $query->limit(24)->get()->map(function ($variant) {
            return [
                'id' => $variant->id,
                'product_name' => $variant->product->name ?? 'Unknown',
                'variant_name' => $variant->name ?: 'Default',
                'sku' => $variant->sku,
                'price' => (float) $variant->price,
                'stock' => $variant->stock,
                'image' => $variant->product->primary_image_url ?? asset('images/no-image.png'),
            ];
        });

        return response()->json(['products' => $variants]);
    }

    /**
     * Process the transaction/checkout.
     */
    public function processTransaction(Request $request)
    {
        $request->validate([
            'items'                    => 'required|array|min:1',
            'items.*.variant_id'       => 'required|exists:product_variants,id',
            'items.*.quantity'         => 'required|integer|min:1',
            'items.*.price'            => 'required|numeric|min:0',
            'items.*.discount'         => 'nullable|numeric|min:0',
            'items.*.cut_amount'       => 'nullable|numeric|min:0',
            'subtotal'                 => 'required|numeric|min:0',
            'discount'                 => 'nullable|numeric|min:0',
            'tax'                      => 'nullable|numeric|min:0',
            'grand_total'             => 'required|numeric|min:0',
            'payment_mode'            => 'required|in:full,partial',
            'payment_method'          => 'required_if:payment_mode,full|string',
            'amount_paid'             => 'required_if:payment_mode,full|numeric',
            'payments'                => 'required|array|min:1',
            'payments.*.amount'       => 'required|numeric|min:0',
            'payments.*.payment_method' => 'required|string',
            'payments.*.payment_date' => 'required|date',
            'payments.*.status'       => 'required|in:paid,pending',
            'customer_id'             => 'nullable|exists:customers,id',
            'customer_name'           => 'nullable|string|max:255',
            'customer_phone'          => 'nullable|string|max:20',
            'notes'                   => 'nullable|string|max:1000',
            'deposit_amount'          => 'nullable|numeric|min:0',
        ]);

        // Validate deposit usage: requires a registered customer + permission
        $depositAmount = (float) ($request->deposit_amount ?? 0);
        if ($depositAmount > 0) {
            abort_if(!auth()->user()->can('Use Deposit'), 403, 'You do not have permission to use deposit.');

            if (!$request->customer_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Deposit can only be used when a registered customer is selected.',
                ], 422);
            }

            $customer = Customer::find($request->customer_id);
            if ((float) $customer->deposit_balance < $depositAmount) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient deposit balance. Available: Rp ' . number_format($customer->deposit_balance, 0, ',', '.'),
                ], 422);
            }
        }

        $paymentsTotal = collect($request->payments)->sum('amount');
        if ($request->payment_mode === 'partial' && $paymentsTotal > $request->grand_total) {
            return response()->json([
                'success' => false,
                'message' => __('pos.amount_insufficient'),
            ], 422);
        }

        // Validate stock availability for all items
        foreach ($request->items as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => __('pos.product_not_found'),
                ], 422);
            }
            if ($variant->stock < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => __('pos.insufficient_stock', ['product' => $variant->full_name, 'stock' => $variant->stock]),
                ], 422);
            }
        }

        // Validate amount paid for full payment (cash/card + deposit must cover grand total)
        if ($request->payment_mode === 'full' && ($request->amount_paid + $depositAmount) < $request->grand_total) {
            return response()->json([
                'success' => false,
                'message' => __('pos.amount_insufficient'),
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Calculate initial paid amount and status
            // Include deposit in the net-paid calculation so the initial transaction status is correct.
            $totalPaidNow = collect($request->payments)
                ->where('status', 'paid')
                ->sum(fn($p) => (float) $p['amount']);

            $totalChange = $request->payment_mode === 'full' ? max(0, $request->amount_paid - $request->grand_total) : 0;
            // Net from cash/card payments + deposit
            $totalNetPaidNow = max(0, $totalPaidNow - $totalChange) + $depositAmount;

            $paymentStatus = 'unpaid';
            if ($totalNetPaidNow >= $request->grand_total) {
                $paymentStatus = 'paid';
            } elseif (($totalPaidNow + $depositAmount) > 0) {
                $paymentStatus = 'partial';
            }

            // Create transaction
            $transaction = Transaction::create([
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'subtotal' => $request->subtotal,
                'discount' => $request->discount ?? 0,
                'tax' => $request->tax ?? 0,
                'grand_total' => $request->grand_total,
                'payment_mode' => $request->payment_mode,
                'payment_status' => $paymentStatus,
                'payment_method' => $request->payment_mode === 'full' ? $request->payment_method : 'multiple',
                'amount_paid' => $totalNetPaidNow,
                'notes' => $request->notes,
                'customer_id' => $request->customer_id,
                'user_id' => auth()->id(),
            ]);

            // Create transaction payments (skip zero-amount entries — they happen when deposit covers 100%)
            $totalChangeToAssign = $request->payment_mode === 'full' ? max(0, $request->amount_paid - $request->grand_total) : 0;
            $paymentsCount = count($request->payments);

            foreach ($request->payments as $index => $paymentData) {
                // Skip zero-amount payment records (deposit-only scenario)
                if ((float) $paymentData['amount'] <= 0) continue;

                $changeForThisPayment = ($index === $paymentsCount - 1) ? $totalChangeToAssign : 0;

                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'amount'         => $paymentData['amount'],
                    'change'         => $changeForThisPayment,
                    'payment_method' => $paymentData['payment_method'],
                    'payment_date'   => $paymentData['payment_date'],
                    'status'         => $paymentData['status'],
                    'notes'          => $paymentData['notes'] ?? null,
                ]);
            }

            // Handle deposit payment (if customer used their deposit balance)
            if ($depositAmount > 0) {
                $customer = Customer::find($request->customer_id);
                $depositEntry = $customer->useDeposit(
                    $depositAmount,
                    $transaction->id,
                    auth()->id()
                );

                TransactionPayment::create([
                    'transaction_id' => $transaction->id,
                    'amount'         => $depositAmount,
                    'change'         => 0,
                    'payment_method' => 'deposit',
                    'payment_date'   => now()->format('Y-m-d'),
                    'status'         => 'paid',
                    'notes'          => 'Paid via customer deposit',
                    'deposit_id'     => $depositEntry->id,
                ]);

                // Refresh payment status after deposit payment is added
                $transaction->refreshPaymentStatus();
            }

            // Create transaction items and deduct stock
            foreach ($request->items as $item) {
                $variant = ProductVariant::with('product')->find($item['variant_id']);
                
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

                // Deduct stock with movement record
                $variant->adjustStock(
                    $item['quantity'],
                    'out',
                    auth()->id(),
                    $transaction->invoice_no,
                    'Sale transaction'
                );
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => __('pos.checkout_success'),
                'transaction' => [
                    'id' => $transaction->id,
                    'invoice_no' => $transaction->invoice_no,
                    'grand_total' => $transaction->grand_total,
                    'change' => $transaction->total_change,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => __('pos.checkout_error') . ': ' . $e->getMessage(),
            ], 500);
        }
    }
}
