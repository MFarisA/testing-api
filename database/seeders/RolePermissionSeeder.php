<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
// use Illuminate\Database\Seeder;
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
        // Buat Permissions
        $permissions = ['edit articles', 'delete articles', 'publish articles', 'view reports'];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // Buat Roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $writer = Role::firstOrCreate(['name' => 'writer']);

        // Assign Permissions ke admin
        $admin->givePermissionTo(Permission::all());

        // Assign sebagian permission ke writer
        $writer->givePermissionTo(['edit articles', 'publish articles']);
    }
}
