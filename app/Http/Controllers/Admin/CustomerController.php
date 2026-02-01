<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\DataTables\CustomersDataTable;
use Illuminate\Http\Request;

use App\DataTables\CustomerTransactionsDataTable;

class CustomerController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->get('term');
        
        $query = Customer::query();

        if ($term) {
            $query->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%")
                  ->orWhere('email', 'like', "%{$term}%");
            });
        }

        $customers = $query->limit(20)->get()->map(function($customer) {
            return [
                'id' => $customer->id,
                'text' => $customer->name . ($customer->phone ? ' (' . $customer->phone . ')' : '')
            ];
        });

        return response()->json($customers);
    }

    public function index(CustomersDataTable $dataTable)
    {
        $groups = \App\Models\CustomerGroup::all();
        return $dataTable->render('admin.customers.index', compact('groups'));
    }

    public function create()
    {
        $groups = \App\Models\CustomerGroup::all();
        return view('admin.customers.create', compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email',
            'phone' => 'nullable|string|max:20|unique:customers,phone',
            'address' => 'nullable|string',
            'customer_group_id' => 'required|exists:customer_groups,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('is_active');
        $data['is_active'] = $request->has('is_active');

        Customer::create($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer, CustomerTransactionsDataTable $dataTable)
    {
        return $dataTable->render('admin.customers.show', compact('customer'));
    }

    public function edit(Customer $customer)
    {
        $groups = \App\Models\CustomerGroup::all();
        return view('admin.customers.edit', compact('customer', 'groups'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . $customer->id,
            'phone' => 'nullable|string|max:20|unique:customers,phone,' . $customer->id,
            'address' => 'nullable|string',
            'customer_group_id' => 'required|exists:customer_groups,id',
            'is_active' => 'boolean',
        ]);

        $data = $request->except('is_active');
        $data['is_active'] = $request->has('is_active');

        $customer->update($data);

        return redirect()->route('admin.customers.index')
            ->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        // Check for transactions
        if ($customer->transactions()->count() > 0) {
             return response()->json(['error' => 'Cannot delete customer with transaction history.'], 422);
        }

        $customer->delete();

        return response()->json(['success' => 'Customer deleted successfully.']);
    }
}
