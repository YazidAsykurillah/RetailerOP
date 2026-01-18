<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\DataTables\ProductsDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(ProductsDataTable $dataTable)
    {
        return $dataTable->render('admin.products.index');
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created product.
     */
    public function store(Request $request)
    {
        $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'base_price' => 'required|numeric|min:0',
            'base_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only([
            'sku', 'name', 'category_id', 'brand_id', 
            'base_price', 'base_cost', 'description', 'short_description'
        ]);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');
        $data['base_cost'] = $request->base_cost ?? 0;

        $product = Product::create($data);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $sortOrder = 0;
            foreach ($request->file('images') as $image) {
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'alt_text' => $product->name,
                    'sort_order' => $sortOrder,
                    'is_primary' => $sortOrder === 0, // First image is primary
                ]);
                $sortOrder++;
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully.',
                'redirect' => route('admin.products.index')
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $product->load('images');
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        
        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified product.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'sku' => 'required|string|max:50|unique:products,sku,' . $product->id,
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'base_price' => 'required|numeric|min:0',
            'base_cost' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = $request->only([
            'sku', 'name', 'category_id', 'brand_id', 
            'base_price', 'base_cost', 'description', 'short_description'
        ]);
        $data['slug'] = Str::slug($request->name);
        $data['is_active'] = $request->has('is_active');
        $data['base_cost'] = $request->base_cost ?? 0;

        $product->update($data);

        // Handle image deletions
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image && $image->product_id == $product->id) {
                    Storage::disk('public')->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        // Handle new image uploads
        if ($request->hasFile('images')) {
            $maxSortOrder = $product->images()->max('sort_order') ?? -1;
            $hasPrimary = $product->images()->where('is_primary', true)->exists();
            
            foreach ($request->file('images') as $image) {
                $maxSortOrder++;
                $path = $image->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'alt_text' => $product->name,
                    'sort_order' => $maxSortOrder,
                    'is_primary' => !$hasPrimary && $maxSortOrder === 0,
                ]);
            }
        }

        // Handle primary image change
        if ($request->has('primary_image')) {
            $product->images()->update(['is_primary' => false]);
            ProductImage::where('id', $request->primary_image)
                ->where('product_id', $product->id)
                ->update(['is_primary' => true]);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully.',
                'redirect' => route('admin.products.index')
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Remove the specified product permanently.
     */
    public function destroy(Product $product)
    {
        // Check if product has variants
        if ($product->variants()->count() > 0) {
            return response()->json([
                'error' => 'Cannot delete product with variants. Please delete all variants first.'
            ], 422);
        }

        // Delete all product images from storage and database
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->forceDelete();
        }

        // Permanently delete the product
        $product->forceDelete();

        return response()->json(['success' => 'Product deleted permanently.']);
    }
}
