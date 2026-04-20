<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\ProductImage;
use App\DataTables\ProductsDataTable;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(ProductsDataTable $dataTable)
    {
        $categories = Category::active()->orderBy('name')->get();
        $brands = Brand::active()->orderBy('name')->get();
        return $dataTable->render('admin.products.index', compact('categories', 'brands'));
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
    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $data['base_cost'] = $data['base_cost'] ?? 0;

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
                'message' => __('product.created'),
                'redirect' => route('admin.products.index')
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', __('product.created'));
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
    public function update(UpdateProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $data['base_cost'] = $data['base_cost'] ?? 0;

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
                'message' => __('product.updated'),
                'redirect' => route('admin.products.index')
            ]);
        }

        return redirect()->route('admin.products.index')
            ->with('success', __('product.updated'));
    }

    /**
     * Remove the specified product permanently.
     */
    public function destroy(Product $product)
    {
        // Check if any variants have transaction history (Sales or Purchases)
        $hasHistory = $product->variants()->whereHas('transactionItems')->exists() || 
                      $product->variants()->whereHas('purchaseDetails')->exists();

        if ($hasHistory) {
            return response()->json([
                'error' => __('product.cannot_delete_has_history')
            ], 422);
        }

        // Delete all product images from storage and database
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
            $image->forceDelete();
        }

        // Permanently delete the product (variants will be deleted by DB cascade)
        $product->forceDelete();

        return response()->json(['success' => __('product.deleted')]);
    }

    /**
     * Remove the specified products permanently in bulk.
     */
    public function bulkDestroy(Request $request)
    {
        $ids = $request->ids;
        if (empty($ids) || !is_array($ids)) {
            return response()->json(['error' => 'No products selected.'], 422);
        }

        $deletedCount = 0;
        $skippedCount = 0;

        foreach ($ids as $id) {
            $product = Product::find($id);
            if (!$product) continue;

            // Check if any variants have transaction history (Sales or Purchases)
            $hasHistory = $product->variants()->whereHas('transactionItems')->exists() || 
                          $product->variants()->whereHas('purchaseDetails')->exists();

            if ($hasHistory) {
                $skippedCount++;
                continue;
            }

            // Delete all product images from storage and database
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->forceDelete();
            }

            // Permanently delete the product (variants will be deleted by DB cascade)
            $product->forceDelete();
            $deletedCount++;
        }

        $message = "Successfully deleted $deletedCount products.";
        if ($skippedCount > 0) {
            $message .= " $skippedCount products were skipped due to transaction history.";
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'deleted_count' => $deletedCount,
            'skipped_count' => $skippedCount
        ]);
    }

    /**
     * Show the product import form.
     */
    public function import()
    {
        return view('admin.products.import');
    }

    /**
     * Process the product import.
     */
    public function processImport(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $updateExisting = $request->has('update_existing');
            
            \Maatwebsite\Excel\Facades\Excel::import(
                new \App\Imports\ProductImport($updateExisting),
                $request->file('file')
            );

            return redirect()->route('admin.products.index')
                ->with('success', __('product.imported'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $messages = [];
            foreach ($failures as $failure) {
                $messages[] = 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Validation failed: ' . implode('<br>', $messages));
        } catch (\Exception $e) {
            return back()->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Download product import template.
     */
    public function downloadTemplate()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ProductTemplateExport, 
            'product_import_template.xlsx'
        );
    }

    /**
     * Export products to PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = Product::query();
        $filenameParts = ['product_list'];

        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
            $category = Category::find($request->category_id);
            if ($category) {
                $filenameParts[] = Str::slug($category->name);
            }
        }

        if ($request->has('brand_id') && $request->brand_id) {
            $query->where('brand_id', $request->brand_id);
            $brand = Brand::find($request->brand_id);
            if ($brand) {
                $filenameParts[] = Str::slug($brand->name);
            }
        }

        $stockOnly = $request->has('stock_only') && $request->stock_only == 'true';
        
        if ($stockOnly) {
            $query->whereHas('variants', function($q) {
                $q->where('stock', '>', 0);
            });
            $query->with(['category', 'brand', 'variants' => function($q) {
                $q->where('stock', '>', 0);
            }]);
            $filenameParts[] = 'stock_only';
        } else {
            $query->with(['category', 'brand', 'variants']);
        }

        $products = $query->orderBy('name', 'asc')->get();
        
        // Simple initialization to rule out option-related errors
        $pdf = Pdf::loadView('admin.products.pdf', compact('products'));
        $pdf->setPaper('a4', 'landscape');
        
        // Use a basic set of stable options
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isPhpEnabled', true); // Still needed for page numbers

        $filename = implode('_', $filenameParts) . '_' . date('YmdHis') . '.pdf';

        // Use Laravel's streamDownload for more reliable header handling
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
