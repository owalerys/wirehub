<?php

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        /**
         * Account Management
         */
        Permission::create(['name' => 'connect account']);
        Permission::create(['name' => 'view all accounts']);
        Permission::create(['name' => 'view own accounts']);

        /**
         * Merchant Management
         */
        Permission::create(['name' => 'create merchant']);
        Permission::create(['name' => 'view all merchants']);

        Role::create(['name' => 'super-admin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'admin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'owner'])->givePermissionTo(['view own accounts']);
        Role::create(['name' => 'member'])->givePermissionTo(['view own accounts']);
    }
}
