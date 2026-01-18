<?php

namespace App\Http\Controllers;

use App\DataTables\RolesDataTable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('roles.index');
    }

    public function create()
    {
        $permissions = Permission::pluck('name','name')->all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permissions'));

        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }

    public function show(string $id)
    {
        $role = Role::find($id);
        $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
            ->where("role_has_permissions.role_id",$id)
            ->get();
        return view('roles.show',compact('role','rolePermissions'));
    }

    public function edit(string $id)
    {
        $role = Role::find($id);
        $permissions = Permission::pluck('name','name')->all();
        $rolePermissions = $role->permissions->pluck('name','name')->all();
        return view('roles.edit',compact('role','permissions','rolePermissions'));
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'permissions' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permissions'));

        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }

    public function destroy(string $id)
    {
        Role::find($id)->delete();
        return response()->json(['success'=>'Role deleted successfully.']);
    }
}
