<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Clear cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        Permission::create(['name' => 'view posts']);
        Permission::create(['name' => 'create posts']);
        Permission::create(['name' => 'edit posts']);
        Permission::create(['name' => 'delete posts']);

        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $profesor = Role::create(['name' => 'profesor']);
        $estudiante = Role::create(['name' => 'estudiante']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());
        $profesor->givePermissionTo(['view posts', 'create posts', 'edit posts']);
        $estudiante->givePermissionTo(['view posts']);
    }
}