<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;


class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view permission', ['only' => ['index']]);
        $this->middleware('permission:create permission', ['only' => ['create','store']]);
        $this->middleware('permission:update permission', ['only' => ['update','edit']]);
        $this->middleware('permission:delete permission', ['only' => ['destroy']]);
    }
    
    public function index()
    {
        $permissions = Permission::get();
        return response()->json(['permissions' => $permissions], Response::HTTP_OK);
    }

    public function storePermission (Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        $permission = Permission::create(['name' => $request->name]);

        return response()->json(['message' => 'Permission created', 'permission' => $permission], Response::HTTP_CREATED);
    }

    public function update (Request $request, Permission $permission)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'unique:permissions,name,' . $permission->id
            ]
        ]);

        $permission->name = $request->name;
        $permission->save();
        return response()->json(['message' => 'Permission updated', 'permission' => $permission], Response::HTTP_OK);
    }
    
    public function destroy (Permission $permission)
    {
        $permission->delete();
        return response()->json(['message' => 'Permission deleted'], Response::HTTP_OK);
    }

}
