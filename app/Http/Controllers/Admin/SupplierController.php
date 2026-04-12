<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\DataTables\SuppliersDataTable;
use App\DataTables\SupplierPurchasesDataTable;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Supplier\StoreSupplierRequest;
use App\Http\Requests\Admin\Supplier\UpdateSupplierRequest;

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
     * Display the specified supplier.
     */
    public function show(Supplier $supplier, SupplierPurchasesDataTable $dataTable)
    {
        return $dataTable->render('admin.suppliers.show', compact('supplier'));
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
    public function store(StoreSupplierRequest $request)
    {
        $data = $request->validated();
        Supplier::create($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', __('supplier.created'));
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
    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $data = $request->validated();
        $supplier->update($data);

        return redirect()->route('admin.suppliers.index')
            ->with('success', __('supplier.updated'));
    }

    /**
     * Remove the specified supplier.
     */
    public function destroy(Supplier $supplier)
    {
        // Check if supplier has stock movements
        if ($supplier->stockMovements()->count() > 0) {
            return response()->json(['error' => __('supplier.cannot_delete')], 422);
        }

        $supplier->delete();

        return response()->json(['success' => __('supplier.deleted')]);
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
