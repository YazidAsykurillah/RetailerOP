<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VariantType;
use App\DataTables\VariantTypesDataTable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VariantTypeController extends Controller
{
    /**
     * Display a listing of variant types.
     */
    public function index(VariantTypesDataTable $dataTable)
    {
        return $dataTable->render('admin.variant-types.index');
    }

    /**
     * Show the form for creating a new variant type.
     */
    public function create()
    {
        return view('admin.variant-types.create');
    }

    /**
     * Store a newly created variant type.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:variant_types,name',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        VariantType::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant type created successfully.',
            'redirect' => route('admin.variant-types.index')
        ]);
    }

    /**
     * Show the form for editing the specified variant type.
     */
    public function edit(VariantType $variantType)
    {
        return view('admin.variant-types.edit', compact('variantType'));
    }

    /**
     * Update the specified variant type.
     */
    public function update(Request $request, VariantType $variantType)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:variant_types,name,' . $variantType->id,
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $variantType->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant type updated successfully.',
            'redirect' => route('admin.variant-types.index')
        ]);
    }

    /**
     * Remove the specified variant type.
     */
    public function destroy(VariantType $variantType)
    {
        // Check if variant type has values that are in use
        $usedValuesCount = $variantType->values()
            ->whereHas('productVariants')
            ->count();

        if ($usedValuesCount > 0) {
            return response()->json([
                'error' => 'Cannot delete variant type. Some values are being used by product variants.'
            ], 422);
        }

        $variantType->delete();

        return response()->json(['success' => 'Variant type deleted successfully.']);
    }
}
