<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\DataTables\BrandsDataTable;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Brand\StoreBrandRequest;
use App\Http\Requests\Admin\Brand\UpdateBrandRequest;
use Illuminate\Support\Str;

class BrandController extends Controller
{
    /**
     * Display a listing of brands.
     */
    public function index(BrandsDataTable $dataTable)
    {
        return $dataTable->render('admin.brands.index');
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('admin.brands.create');
    }

    /**
     * Store a newly created brand.
     */
    public function store(StoreBrandRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully.');
    }

    /**
     * Show the form for editing the specified brand.
     */
    public function edit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    /**
     * Update the specified brand.
     */
    public function update(UpdateBrandRequest $request, Brand $brand)
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($brand->logo) {
                \Storage::disk('public')->delete($brand->logo);
            }
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully.');
    }

    /**
     * Remove the specified brand.
     */
    public function destroy(Brand $brand)
    {
        // Check if brand has products
        if ($brand->products()->count() > 0) {
            return response()->json(['error' => 'Cannot delete brand with products.'], 422);
        }

        // Delete logo if exists
        if ($brand->logo) {
            \Storage::disk('public')->delete($brand->logo);
        }

        $brand->delete();

        return response()->json(['success' => 'Brand deleted successfully.']);
    }
}
