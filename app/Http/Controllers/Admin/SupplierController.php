<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\DataTables\SuppliersDataTable;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Display a listing of suppliers.
     */
    public function index(SuppliersDataTable $dataTable)
    {
        return $dataTable->render('admin.suppliers.index');
    }

    /**
     * Show the form for creating a new supplier.
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store a newly created supplier.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email',
            'phone' => 'required|string|max:255|unique:suppliers,phone',
            'address' => 'required|string',
            'website' => 'nullable|url|max:255',
            'tax_id' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('is_active');
        $data['is_active'] = $request->has('is_active');

        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Show the form for editing the specified supplier.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified supplier.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:suppliers,name,' . $supplier->id,
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:suppliers,email,' . $supplier->id,
            'phone' => 'required|string|max:255|unique:suppliers,phone,' . $supplier->id,
            'address' => 'required|string',
            'website' => 'nullable|url|max:255',
            'tax_id' => 'nullable|string|max:255',
            'payment_terms' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('is_active');
        $data['is_active'] = $request->has('is_active');

        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier)
    {
        // Check if supplier has stock movements
        if ($supplier->stockMovements()->count() > 0) {
            return response()->json(['error' => 'Cannot delete supplier with stock movement history.'], 422);
        }

        $supplier->delete();

        return response()->json(['success' => 'Supplier deleted successfully.']);
    }
    public function search(Request $request)
    {
        $term = $request->term;
        $suppliers = Supplier::where('name', 'like', "%{$term}%")
            ->where('is_active', true)
            ->get();

        return response()->json($suppliers->map(function ($supplier) {
            return [
                'id' => $supplier->id,
                'text' => $supplier->name
            ];
        }));
    }
}
