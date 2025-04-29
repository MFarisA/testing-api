<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view role', ['only' => ['index']]);
        $this->middleware('permission:create role', ['only' => ['create', 'store', 'addPermissionToRole', 'givePermissionToRole']]);
        $this->middleware('permission:update role', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete role', ['only' => ['destroy']]);
    }

    public function index( Request $request)
    {
        $roles = Role::get();
        return response()->json(['roles' => $roles], Response::HTTP_OK);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = Role::create(['name' => $request->name]);

        return response()->json(['message' => 'Role created', 'role' => $role], Response::HTTP_CREATED);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:roles,name,' . $role->id
            ]
        ]);

        $role->name = $request->name;
        $role->save();
        return response()->json(['message' => 'Role updated', 'role' => $role], Response::HTTP_OK);
    }

    public function addPermissionToRole(string $id)
    {

        $permissions = Permission::get();
        $role = Role::findOrFail($id);
        $rolePermissions = DB::table('role_has_permissions')
            ->where('role_has_permissions.role_id', $role->id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        $permissions = Permission::all();

        return response()->json([
            'message' => 'Permission added to role',
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions
        ], Response::HTTP_OK);
    }

    public function givePermissionToRole(Request $request, string $id)
    {
        $request->validate([
            'permission' => 'required|exists:permissions,name',
        ]);

        $role = Role::findOrFail($id);
        $role->syncPermissions($request->permission);

        return response()->json(['message' => 'Permission given to role', 'role' => $role], Response::HTTP_OK);
    }


    public function destroyRole(string $id)
    {
        $role = Role::findById($id);
        if ($role) {
            $role->delete();
            return response()->json(['message' => 'Role deleted', 'role' => $role->name], Response::HTTP_OK);
        } else {
            return response()->json(['message' => 'Role not found'], Response::HTTP_NOT_FOUND);
        }
    }
}
