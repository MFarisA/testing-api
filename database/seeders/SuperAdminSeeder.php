<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'api']);

        // $permissions = Permission::all();
        $permissions = Permission::where('guard_name', 'api')->get();
        $role->syncPermissions($permissions);

        $user = User::firstOrCreate(
            ['email' => 'superAdmin@x.com'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('katasandi123'), 
            ]
        );

        $user->assignRole($role);
    }
}
