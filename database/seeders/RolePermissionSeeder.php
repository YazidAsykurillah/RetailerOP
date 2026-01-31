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
        Permission::create(['name' => 'Manage Roles']);
        Permission::create(['name' => 'Manage Permissions']);
        Permission::create(['name' => 'Access Store Management']);
        Permission::create(['name' => 'Manage Categories']);
        Permission::create(['name' => 'Manage Brands']);
        Permission::create(['name' => 'Manage Suppliers']);
        Permission::create(['name' => 'Manage Customers']);
        Permission::create(['name' => 'Manage Variant Types']);
        Permission::create(['name' => 'Access Inventory']);
        Permission::create(['name' => 'Manage Stock']);
        Permission::create(['name' => 'Access Pos']);
        Permission::create(['name' => 'Manage Purchases']);
        Permission::create(['name' => 'Manage Customers']);

        // Create Roles and Assign Permissions

        // Super Admin
        $superAdmin = Role::create(['name' => 'Super Admin']);
        // Super Admin gets all permissions via Gate::before rule usually, but for explicit permission we can give all
        $superAdmin->givePermissionTo(Permission::all());

        // Admin
        $admin = Role::create(['name' => 'Admin']);
        $admin->givePermissionTo([
            'Manage Products', 'Manage Orders', 'Access Store Management',
            'Manage Categories', 'Manage Brands', 'Manage Suppliers', 'Manage Customers', 'Manage Variant Types',
            'Access Inventory', 'Manage Stock', 'Access Pos','Manage Purchases', 'Manage Customers'
        ]);

        // Cashier
        $cashier = Role::create(['name' => 'Cashier']);
        $cashier->givePermissionTo([
            'Access Pos'
        ]);

        // Warehouse Staff
        $warehouse_staff = Role::create(['name' => 'Warehouse Staff']);
        $warehouse_staff->givePermissionTo([
            'Access Inventory', 'Manage Stock'
        ]);

        // Purchasing Staff
        $purchasing_staff = Role::create(['name' => 'Purchasing Staff']);
        $purchasing_staff->givePermissionTo([
            'Manage Purchases',
        ]);


        // Create a Demo Super Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password'), // Default password
        ]);
        $user->assignRole($superAdmin);

        // Create a Demo Admin User
        $user = \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
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

        // Create a Demo Warehouse Staff User
        $user = \App\Models\User::factory()->create([
            'name' => 'Warehouse Staff User',
            'email' => 'warehouse_staff@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($warehouse_staff);

        // Create a Demo Purchasing Staff User
        $user = \App\Models\User::factory()->create([
            'name' => 'Purchasing Staff User',
            'email' => 'purchasing_staff@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->assignRole($purchasing_staff);
    }
}
