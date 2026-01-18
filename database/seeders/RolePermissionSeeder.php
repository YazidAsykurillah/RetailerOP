<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage products']);
        Permission::create(['name' => 'manage orders']);

        // Create Roles and Assign Permissions

        // Super Admin
        $superAdmin = Role::create(['name' => 'Super Admin']);
        // Super Admin gets all permissions via Gate::before rule usually, but for explicit permission we can give all
        $superAdmin->givePermissionTo(Permission::all());

        // Admin
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo(['manage products', 'manage orders']);

        // Customer
        $customer = Role::create(['name' => 'Customer']);
        // Customer mostly has no specific backend permissions, logic usually handled by policy or separate scope

        // Create a Demo Super Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'), // Default password
        ]);
        $user->assignRole($superAdmin);

        // Create a Demo Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'manager@admin.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($admin);

         // Create a Demo Customer User
         $user = \App\Models\User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@gmail.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($customer);
    }
}
