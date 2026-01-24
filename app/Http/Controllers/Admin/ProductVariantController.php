<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantType;
use App\Models\VariantValue;
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
            'variant_types' => 'nullable|array',
            'variant_types.*' => 'nullable|exists:variant_types,id',
            'variant_values' => 'nullable|array',
            'variant_values.*' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Process text-based variant values and get/create VariantValue IDs
        $variantValueIds = $this->processVariantValues(
            $request->variant_types ?? [],
            $request->variant_values ?? []
        );

        // Check for duplicate variant combination
        if (!empty($variantValueIds) && ProductVariant::combinationExists($product->id, $variantValueIds)) {
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

        // Attach variant values
        if (!empty($variantValueIds)) {
            $variant->variantValues()->attach($variantValueIds);
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
            'variant_types' => 'nullable|array',
            'variant_types.*' => 'nullable|exists:variant_types,id',
            'variant_values' => 'nullable|array',
            'variant_values.*' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        // Process text-based variant values and get/create VariantValue IDs
        $variantValueIds = $this->processVariantValues(
            $request->variant_types ?? [],
            $request->variant_values ?? []
        );

        // Check for duplicate variant combination (excluding current variant)
        if (!empty($variantValueIds) && ProductVariant::combinationExists($product->id, $variantValueIds, $variant->id)) {
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

        // Sync variant values
        $variant->variantValues()->sync($variantValueIds);

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

    /**
     * Print barcode for the specified variant.
     */
    public function printBarcode(\Illuminate\Http\Request $request, Product $product, $variantId)
    {
        // Explicitly find the variant
        $variant = ProductVariant::where('product_id', $product->id)
            ->where('id', $variantId)
            ->firstOrFail();

        $qty = $request->input('qty', 1);

        $generator = new \Picqer\Barcode\BarcodeGeneratorSVG();
        $barcode = $generator->getBarcode($variant->sku, $generator::TYPE_CODE_128);

        return view('admin.products.variants.print-barcode', compact('product', 'variant', 'barcode', 'qty'));
    }

    /**
     * Process text-based variant values and get/create VariantValue IDs.
     *
     * @param array $variantTypeIds Array of variant type IDs
     * @param array $variantValues Array of text values corresponding to each type
     * @return array Array of VariantValue IDs
     */
    private function processVariantValues(array $variantTypeIds, array $variantValues): array
    {
        $valueIds = [];

        foreach ($variantTypeIds as $index => $typeId) {
            $textValue = trim($variantValues[$index] ?? '');
            
            // Skip empty values
            if (empty($textValue) || empty($typeId)) {
                continue;
            }

            // Try to find existing value or create new one
            $variantValue = VariantValue::firstOrCreate(
                [
                    'variant_type_id' => $typeId,
                    'value' => $textValue,
                ],
                [
                    'sort_order' => 0,
                ]
            );

            $valueIds[] = $variantValue->id;
        }

        return $valueIds;
    }
}
