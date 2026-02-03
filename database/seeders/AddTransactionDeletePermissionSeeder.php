<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddTransactionDeletePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Permission
        $permission = Permission::firstOrCreate(['name' => 'Delete Transaction']);

        // Assign to Roles
        $roles = ['Super Admin', 'Admin'];

        foreach ($roles as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo($permission);
            }
        }
    }
}
