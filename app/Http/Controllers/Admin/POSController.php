<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Transaction;
use App\Models\TransactionItem;
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
     * Get product variant details.
     */
    public function getProductDetails($id)
    {
        $variant = ProductVariant::with(['product.primaryImage', 'variantValues'])->find($id);

        if (!$variant) {
            return response()->json(['success' => false, 'message' => 'Product not found'], 404);
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
            'items' => 'required|array|min:1',
            'items.*.variant_id' => 'required|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'items.*.discount' => 'nullable|numeric|min:0',
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

        // Validate stock availability for all items
        foreach ($request->items as $item) {
            $variant = ProductVariant::find($item['variant_id']);
            if (!$variant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product variant not found.',
                ], 422);
            }
            if ($variant->stock < $item['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => "Insufficient stock for {$variant->full_name}. Available: {$variant->stock}",
                ], 422);
            }
        }

        // Validate amount paid
        if ($request->amount_paid < $request->grand_total) {
            return response()->json([
                'success' => false,
                'message' => 'Amount paid is less than the total amount.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Create transaction
            $transaction = Transaction::create([
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
                'user_id' => auth()->id(),
            ]);

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
                    'subtotal' => ($item['price'] * $item['quantity']) - ($item['discount'] ?? 0),
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
                'message' => 'Transaction completed successfully!',
                'transaction' => [
                    'id' => $transaction->id,
                    'invoice_no' => $transaction->invoice_no,
                    'grand_total' => $transaction->grand_total,
                    'change' => $transaction->change,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process transaction: ' . $e->getMessage(),
            ], 500);
        }
    }
}
