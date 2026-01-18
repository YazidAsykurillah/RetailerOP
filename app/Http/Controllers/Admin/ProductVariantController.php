<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantType;
use App\DataTables\ProductVariantsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of variants for a product.
     */
    public function index(Product $product, ProductVariantsDataTable $dataTable)
    {
        return $dataTable->forProduct($product->id)->render('admin.products.variants.index', [
            'product' => $product,
        ]);
    }

    /**
     * Show the form for creating a new variant.
     */
    public function create(Product $product)
    {
        $variantTypes = VariantType::with('values')->orderBy('sort_order')->get();
        return view('admin.products.variants.create', compact('product', 'variantTypes'));
    }

    /**
     * Store a newly created variant.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'required|string|max:50|unique:product_variants,sku',
            'name' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'variant_values' => 'required|array',
            'variant_values.*' => 'required|exists:variant_values,id',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate variant combination
        $variantValues = $request->variant_values ?? [];
        if (ProductVariant::combinationExists($product->id, $variantValues)) {
            return response()->json([
                'message' => 'A variant with this attribute combination already exists.',
                'errors' => [
                    'variant_values' => ['A variant with this exact attribute combination already exists for this product.']
                ]
            ], 422);
        }

        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $request->sku,
            'name' => $request->name,
            'price' => $request->price,
            'cost' => $request->cost ?? 0,
            'stock' => $request->stock,
            'min_stock' => $request->min_stock ?? 5,
            'is_active' => $request->has('is_active'),
        ]);

        // Attach variant values (filter out empty values)
        $filteredValues = array_filter($variantValues, fn($v) => !empty($v));
        if (!empty($filteredValues)) {
            $variant->variantValues()->attach($filteredValues);
        }

        return response()->json([
            'success' => true,
            'message' => 'Variant created successfully.',
            'redirect' => route('admin.products.variants.index', $product->id)
        ]);
    }

    /**
     * Show the form for editing the specified variant.
     */
    public function edit(Product $product, $variantId)
    {
        // Explicitly find the variant to ensure we get the correct record
        $variant = ProductVariant::where('product_id', $product->id)
            ->where('id', $variantId)
            ->firstOrFail();

        $variant->load('variantValues');
        $variantTypes = VariantType::with('values')->orderBy('sort_order')->get();
        $selectedValues = $variant->variantValues->pluck('id')->toArray();

        return view('admin.products.variants.edit', compact('product', 'variant', 'variantTypes', 'selectedValues'));
    }

    /**
     * Update the specified variant.
     */
    public function update(Request $request, Product $product, $variantId)
    {
        // Explicitly find the variant to ensure we get the correct record
        $variant = ProductVariant::where('product_id', $product->id)
            ->where('id', $variantId)
            ->firstOrFail();

        $request->validate([
            'sku' => 'required|string|max:50|unique:product_variants,sku,' . $variant->id,
            'name' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'variant_values' => 'required|array',
            'variant_values.*' => 'required|exists:variant_values,id',
            'is_active' => 'boolean',
        ]);

        // Check for duplicate variant combination (excluding current variant)
        $variantValues = $request->variant_values ?? [];
        if (ProductVariant::combinationExists($product->id, $variantValues, $variant->id)) {
            return response()->json([
                'message' => 'A variant with this attribute combination already exists.',
                'errors' => [
                    'variant_values' => ['A variant with this exact attribute combination already exists for this product.']
                ]
            ], 422);
        }

        // Update with explicit type casting
        $variant->sku = $request->sku;
        $variant->name = $request->name;
        $variant->price = (float) $request->price;
        $variant->cost = (float) ($request->cost ?? 0);
        $variant->stock = (int) $request->stock;
        $variant->min_stock = (int) ($request->min_stock ?? 5);
        $variant->is_active = $request->has('is_active');
        $variant->save();

        // Sync variant values (filter out empty values)
        $filteredValues = array_filter($variantValues, fn($v) => !empty($v));
        $variant->variantValues()->sync($filteredValues);

        return response()->json([
            'success' => true,
            'message' => 'Variant updated successfully.',
            'redirect' => route('admin.products.variants.index', $product->id)
        ]);
    }

    /**
     * Remove the specified variant.
     */
    public function destroy(Product $product, $variantId)
    {
        // Explicitly find the variant to ensure we get the correct record
        $variant = ProductVariant::where('product_id', $product->id)
            ->where('id', $variantId)
            ->firstOrFail();

        $variant->delete();

        return response()->json(['success' => 'Variant deleted successfully.']);
    }
}
