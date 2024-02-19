<?php

use App\Vinnies\Access;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset cache
        //app()['cache']->forget('spatie.permission.cache');
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        // Update roles
        $roles_db = Role::all()->pluck('name');
        $roles    = Access::getRoles();

        $roles_to_delete = $roles_db->diff($roles);
        $roles_to_add    = $roles->diff($roles_db);

        $roles_to_delete->each(function ($role) {
            Role::where('name', $role)->delete();
        });

        $roles_to_add->each(function ($role) {
            Role::create(['name' => $role]);
        });

        // Update permissions
        $permissions_db = Permission::all()->pluck('name');
        $permissions    = Access::getPermissions();

        $permissions_to_delete = $permissions_db->diff($permissions);
        $permissions_to_add    = $permissions->diff($permissions_db);

        $permissions_to_delete->each(function ($permission) {
            Permission::where('name', $permission)->delete();
        });

        $permissions_to_add->each(function ($permission) {
            Permission::create(['name' => $permission]);
        });

        // Update roles-permission relationship
        Access::get()->each(function ($permissions, $role) {
            Role::whereName($role)
                ->first()
                ->syncPermissions($permissions);
        });
    }
}
