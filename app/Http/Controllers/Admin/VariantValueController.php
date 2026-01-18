<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VariantType;
use App\Models\VariantValue;
use App\DataTables\VariantValuesDataTable;
use Illuminate\Http\Request;

class VariantValueController extends Controller
{
    /**
     * Display a listing of variant values for a type.
     */
    public function index(VariantType $variantType, VariantValuesDataTable $dataTable)
    {
        return $dataTable->forVariantType($variantType->id)->render('admin.variant-types.values.index', [
            'variantType' => $variantType,
        ]);
    }

    /**
     * Show the form for creating a new variant value.
     */
    public function create(VariantType $variantType)
    {
        return view('admin.variant-types.values.create', compact('variantType'));
    }

    /**
     * Store a newly created variant value.
     */
    public function store(Request $request, VariantType $variantType)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Check for duplicate value within the same type
        $exists = VariantValue::where('variant_type_id', $variantType->id)
            ->where('value', $request->value)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This value already exists for this variant type.',
                'errors' => ['value' => ['This value already exists for this variant type.']]
            ], 422);
        }

        VariantValue::create([
            'variant_type_id' => $variantType->id,
            'value' => $request->value,
            'color_code' => $request->color_code,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant value created successfully.',
            'redirect' => route('admin.variant-types.values.index', $variantType->id)
        ]);
    }

    /**
     * Show the form for editing the specified variant value.
     */
    public function edit(VariantType $variantType, VariantValue $value)
    {
        return view('admin.variant-types.values.edit', compact('variantType', 'value'));
    }

    /**
     * Update the specified variant value.
     */
    public function update(Request $request, VariantType $variantType, VariantValue $value)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'color_code' => 'nullable|string|max:20',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        // Check for duplicate value within the same type (excluding current)
        $exists = VariantValue::where('variant_type_id', $variantType->id)
            ->where('value', $request->value)
            ->where('id', '!=', $value->id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'This value already exists for this variant type.',
                'errors' => ['value' => ['This value already exists for this variant type.']]
            ], 422);
        }

        $value->update([
            'value' => $request->value,
            'color_code' => $request->color_code,
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Variant value updated successfully.',
            'redirect' => route('admin.variant-types.values.index', $variantType->id)
        ]);
    }

    /**
     * Remove the specified variant value.
     */
    public function destroy(VariantType $variantType, VariantValue $value)
    {
        // Check if value is being used by product variants
        $usageCount = $value->productVariants()->count();

        if ($usageCount > 0) {
            return response()->json([
                'error' => 'Cannot delete this value. It is being used by ' . $usageCount . ' product variant(s).'
            ], 422);
        }

        $value->delete();

        return response()->json(['success' => 'Variant value deleted successfully.']);
    }
}
