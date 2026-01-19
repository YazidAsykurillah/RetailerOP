<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\Product;
use App\Models\Supplier;
use App\DataTables\StockDataTable;
use App\DataTables\StockMovementsDataTable;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Display stock overview - list all product variants with stock info.
     */
    public function index(StockDataTable $dataTable)
    {
        // Get summary statistics
        $totalItems = ProductVariant::count();
        $lowStockCount = ProductVariant::lowStock()->count();
        $outOfStockCount = ProductVariant::where('stock', '<=', 0)->count();

        return $dataTable->render('admin.stock.index', compact(
            'totalItems',
            'lowStockCount',
            'outOfStockCount'
        ));
    }

    /**
     * Show Stock In form.
     */
    public function createIn(Request $request)
    {
        $selectedVariant = null;
        if ($request->has('variant')) {
            $selectedVariant = ProductVariant::with('product')->find($request->variant);
        }

        $suppliers = Supplier::active()->orderBy('name')->get();

        return view('admin.stock.in', compact('selectedVariant', 'suppliers'));
    }

    /**
     * Process Stock In.
     */
    public function storeIn(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $variant = ProductVariant::findOrFail($request->product_variant_id);
        
        $variant->adjustStock(
            $request->quantity,
            'in',
            auth()->id(),
            $request->reference,
            $request->notes,
            $request->supplier_id
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock added successfully! New stock: ' . $variant->stock,
            ]);
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock added successfully! New stock: ' . $variant->stock);
    }

    /**
     * Show Stock Out form.
     */
    public function createOut(Request $request)
    {
        $selectedVariant = null;
        if ($request->has('variant')) {
            $selectedVariant = ProductVariant::with('product')->find($request->variant);
        }

        return view('admin.stock.out', compact('selectedVariant'));
    }

    /**
     * Process Stock Out.
     */
    public function storeOut(Request $request)
    {
        $request->validate([
            'product_variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $variant = ProductVariant::findOrFail($request->product_variant_id);

        // Validate sufficient stock
        if ($variant->stock < $request->quantity) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock! Current stock: ' . $variant->stock,
                ], 422);
            }

            return back()->withErrors(['quantity' => 'Insufficient stock! Current stock: ' . $variant->stock])
                ->withInput();
        }

        $variant->adjustStock(
            $request->quantity,
            'out',
            auth()->id(),
            $request->reference,
            $request->notes
        );

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Stock removed successfully! Remaining stock: ' . $variant->stock,
            ]);
        }

        return redirect()->route('admin.stock.index')
            ->with('success', 'Stock removed successfully! Remaining stock: ' . $variant->stock);
    }

    /**
     * Show movement history for a specific variant.
     */
    public function history(StockMovementsDataTable $dataTable, $variantId)
    {
        $variant = ProductVariant::with('product')->findOrFail($variantId);

        return $dataTable
            ->forVariant($variantId)
            ->render('admin.stock.history', compact('variant'));
    }

    /**
     * AJAX endpoint for searching products/variants.
     */
    public function searchProducts(Request $request)
    {
        $search = $request->get('q', '');

        $variants = ProductVariant::with('product')
            ->whereHas('product', function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orWhere('sku', 'like', "%{$search}%")
            ->orWhere('name', 'like', "%{$search}%")
            ->limit(20)
            ->get()
            ->map(function ($variant) {
                $productName = $variant->product->name ?? 'Unknown Product';
                $variantName = $variant->name ?: 'Default';
                return [
                    'id' => $variant->id,
                    'text' => "{$productName} - {$variantName} (SKU: {$variant->sku}) [Stock: {$variant->stock}]",
                    'stock' => $variant->stock,
                ];
            });

        return response()->json(['results' => $variants]);
    }

    /**
     * Get variant details for AJAX request.
     */
    public function getVariant(Request $request)
    {
        $variant = ProductVariant::with('product')->find($request->id);

        if (!$variant) {
            return response()->json(['success' => false], 404);
        }

        return response()->json([
            'success' => true,
            'variant' => [
                'id' => $variant->id,
                'name' => $variant->full_name,
                'sku' => $variant->sku,
                'stock' => $variant->stock,
                'min_stock' => $variant->min_stock,
            ],
        ]);
    }
}
