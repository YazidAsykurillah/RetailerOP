<?php

namespace App\Http\Controllers;

use App\DataTables\PermissionsDataTable;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(PermissionsDataTable $dataTable)
    {
        return $dataTable->render('permissions.index');
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:permissions,name',
        ]);

        $name = \Illuminate\Support\Str::title($request->input('name'));
        Permission::create(['name' => $name]);

        return redirect()->route('permissions.index')
                        ->with('success','Permission created successfully');
    }

    public function show(string $id)
    {
        $permission = Permission::find($id);
        return view('permissions.show',compact('permission'));
    }

    public function edit(string $id)
    {
        $permission = Permission::find($id);
        return view('permissions.edit',compact('permission'));
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
        ]);

        $permission = Permission::find($id);
        $permission->name = \Illuminate\Support\Str::title($request->input('name'));
        $permission->save();

        return redirect()->route('permissions.index')
                        ->with('success','Permission updated successfully');
    }

    public function destroy(string $id)
    {
        Permission::find($id)->delete();
        return response()->json(['success'=>'Permission deleted successfully.']);
    }
}
