<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\DataTables\CustomerGroupsDataTable;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function index(CustomerGroupsDataTable $dataTable)
    {
        return $dataTable->render('admin.customer-groups.index');
    }

    public function create()
    {
        return view('admin.customer-groups.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:customer_groups,code',
            'percentage_discount' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
        ]);

        $data = $request->except('is_default');
        $data['is_default'] = $request->has('is_default');

        // If this is set as default, unset other defaults
        if ($data['is_default']) {
            CustomerGroup::where('is_default', true)->update(['is_default' => false]);
        }

        CustomerGroup::create($data);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Customer Group created successfully.');
    }

    public function edit(CustomerGroup $customerGroup)
    {
        return view('admin.customer-groups.edit', compact('customerGroup'));
    }

    public function update(Request $request, CustomerGroup $customerGroup)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:customer_groups,code,' . $customerGroup->id,
            'percentage_discount' => 'required|numeric|min:0|max:100',
            'is_default' => 'boolean',
        ]);

        $data = $request->except('is_default');
        $data['is_default'] = $request->has('is_default');

        // If this is set as default, unset other defaults
        if ($data['is_default']) {
            CustomerGroup::where('is_default', true)->where('id', '!=', $customerGroup->id)->update(['is_default' => false]);
        }

        $customerGroup->update($data);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', 'Customer Group updated successfully.');
    }

    public function destroy(CustomerGroup $customerGroup)
    {
        if ($customerGroup->is_default) {
            return response()->json(['error' => 'Cannot delete the default customer group.'], 422);
        }
        
        if ($customerGroup->customers()->count() > 0) {
            return response()->json(['error' => 'Cannot delete group with associated customers.'], 422);
        }

        $customerGroup->delete();

        return response()->json(['success' => 'Customer Group deleted successfully.']);
    }
}
