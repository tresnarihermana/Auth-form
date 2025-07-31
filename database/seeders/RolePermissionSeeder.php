<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Clear cache permission
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Buat permission
        $permissions = [
            "users.view",
            "users.create",
            "users.edit",
            "users.delete",
            "users.show",
            "users.toggleStatus",
            "roles.view",
            "roles.create",
            "roles.edit",
            "roles.delete", 
            "permissions.view",
            "permissions.create",
            "permissions.edit",
            "permissions.delete",
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Buat role dan assign permission
        $adminRole = Role::firstOrCreate(['name' => 'Super Admin']);
        $adminRole->syncPermissions($permissions);
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(['users.view', 'roles.view', 'users.create', 'users.edit', 'users.delete',]);
        $adminRole = Role::firstOrCreate(['name' => 'operator']);
        $adminRole->syncPermissions(['']);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->givePermissionTo(['']);
    }
}

