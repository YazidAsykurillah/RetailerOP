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
        Permission::create(['name' => 'Manage Users']);
        Permission::create(['name' => 'Manage Products']);
        Permission::create(['name' => 'Manage Orders']);

        // Create Roles and Assign Permissions

        // Super Admin
        $superAdmin = Role::create(['name' => 'Super Admin']);
        // Super Admin gets all permissions via Gate::before rule usually, but for explicit permission we can give all
        $superAdmin->givePermissionTo(Permission::all());

        // Admin
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo(['Manage Products', 'Manage Orders']);

        // Cashier
        $cashier = Role::create(['name' => 'Cashier']);
        // Cashier mostly has no specific backend permissions, logic usually handled by policy or separate scope

        // Create a Demo Super Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'), // Default password
        ]);
        $user->assignRole($superAdmin);

        // Create a Demo Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'manager@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($admin);

         // Create a Demo Cashier User
         $user = \App\Models\User::factory()->create([
            'name' => 'Cashier User',
            'email' => 'cashier@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($cashier);
    }
}
