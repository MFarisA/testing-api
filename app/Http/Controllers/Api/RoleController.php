<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeRole(Request $request)
    {
        $request()->validate([
            'name' => 'required|unique:roles,name',
        ]);
        
        $role = Role::create(['name' => $request->name]);

        return response()->json(['message' => 'Role created', 'role' => $role], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function storePermission(Request $request)
    {
        $request->validate([
           'name' => 'required|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json(['message' => 'Permission created', 'permission' => $permission], Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     */
    public function assignPermissionToRole(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
            'permission' => 'required|exists:permissions,name',
        ]);

        $role = Role::findByName($request->role);
        $role->givePermissionTo($request->permission);

        return response()->json(['message' => 'Permission assigned to role', 'role' => $role->name, 'permission' => $request->permission], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
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
