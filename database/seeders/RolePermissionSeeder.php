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
        // Create Permissions
        Permission::firstOrCreate(['name' => 'View All Users']);
        Permission::firstOrCreate(['name' => 'Manage Users']);
        Permission::firstOrCreate(['name' => 'Manage Products']);
        Permission::firstOrCreate(['name' => 'Manage Orders']);
        Permission::firstOrCreate(['name' => 'Manage Roles']);
        Permission::firstOrCreate(['name' => 'Manage Permissions']);
        Permission::firstOrCreate(['name' => 'Access Store Management']);
        Permission::firstOrCreate(['name' => 'Manage Categories']);
        Permission::firstOrCreate(['name' => 'Manage Brands']);
        Permission::firstOrCreate(['name' => 'Manage Suppliers']);
        Permission::firstOrCreate(['name' => 'Manage Customers']);
        Permission::firstOrCreate(['name' => 'Manage Variant Types']);
        Permission::firstOrCreate(['name' => 'Access Inventory']);
        Permission::firstOrCreate(['name' => 'Manage Stock']);
        Permission::firstOrCreate(['name' => 'Access Pos']);
        Permission::firstOrCreate(['name' => 'Manage Purchases']);
        Permission::firstOrCreate(['name' => 'Manage Customers']);
        Permission::firstOrCreate(['name' => 'Manage Settings']);

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
            'Access Inventory', 'Manage Stock', 'Access Pos','Manage Purchases', 'Manage Customers',
            'Manage Settings'
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
