<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomerGroup;
use App\DataTables\CustomerGroupsDataTable;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CustomerGroup\StoreCustomerGroupRequest;
use App\Http\Requests\Admin\CustomerGroup\UpdateCustomerGroupRequest;

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

    public function store(StoreCustomerGroupRequest $request)
    {
        $data = $request->validated();

        // If this is set as default, unset other defaults
        if ($data['is_default']) {
            CustomerGroup::where('is_default', true)->update(['is_default' => false]);
        }

        CustomerGroup::create($data);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', __('customer.group_created'));
    }

    public function edit(CustomerGroup $customerGroup)
    {
        return view('admin.customer-groups.edit', compact('customerGroup'));
    }

    public function update(UpdateCustomerGroupRequest $request, CustomerGroup $customerGroup)
    {
        $data = $request->validated();

        // If this is set as default, unset other defaults
        if ($data['is_default']) {
            CustomerGroup::where('is_default', true)->where('id', '!=', $customerGroup->id)->update(['is_default' => false]);
        }

        $customerGroup->update($data);

        return redirect()->route('admin.customer-groups.index')
            ->with('success', __('customer.group_updated'));
    }

    public function destroy(CustomerGroup $customerGroup)
    {
        if ($customerGroup->is_default) {
            return response()->json(['error' => __('general.something_went_wrong')], 422);
        }
        
        if ($customerGroup->customers()->count() > 0) {
            return response()->json(['error' => __('general.cannot_delete_has_relations', ['item' => __('customer.singular'), 'relation' => __('customer.group')])], 422);
        }

        $customerGroup->delete();

        return response()->json(['success' => __('customer.group_deleted')]);
    }
}
