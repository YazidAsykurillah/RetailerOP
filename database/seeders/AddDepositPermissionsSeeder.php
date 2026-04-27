<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AddDepositPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $manageDeposit = Permission::firstOrCreate(['name' => 'Manage Deposits']);
        $useDeposit    = Permission::firstOrCreate(['name' => 'Use Deposit']);

        // Super Admin & Admin: full deposit access
        foreach (['Super Admin', 'Admin'] as $roleName) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $role->givePermissionTo([$manageDeposit, $useDeposit]);
            }
        }

        // Cashier: can only use (apply) deposits, not manage them
        $cashier = Role::where('name', 'Cashier')->first();
        if ($cashier) {
            $cashier->givePermissionTo($useDeposit);
        }
    }
}
