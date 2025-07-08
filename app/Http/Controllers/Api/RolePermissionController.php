<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionController extends Controller
{
    public function indexRoles()
    {
        $roles = Role::with('permissions')->where('name', '!=', 'super-admin')->get();
        return response()->json($roles);
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api',
        ]);

        // Assign permissions jika ada (untuk checklist UI)
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissions = Permission::whereIn('id', $request->permissions)->get();
            $role->syncPermissions($permissions);
        }

        // Get all permissions grouped by category for response
        $allPermissions = \Spatie\Permission\Models\Permission::all()
            ->groupBy('kategori')
            ->mapWithKeys(function ($items, $key) {
                return [$key => $items->sortBy('order')->values()];
            })
            ->sortKeys();

        return response()->json([
            'message' => 'Role created successfully',
            'data' => $role->load('permissions'),
            'available_permissions' => $allPermissions,
            'permissions_grouped' => $allPermissions,
        ], 201);
    }

    public function updateRole(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        if ($role->name === 'super-admin') {
            return response()->json(['message' => 'Cannot update super-admin role'], 403);
        }

        $request->validate([
            'name' => 'sometimes|required|string|unique:roles,name,' . $id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        // Store old name for logging
        $oldName = $role->name;

        // Update role name if provided
        if ($request->has('name')) {
            $role->name = $request->name;
            $role->save();
        }

        // Update permissions - sync with selected permissions
        if ($request->has('permissions')) {
            if (is_array($request->permissions)) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $role->syncPermissions($permissions);
            } else {
                $role->syncPermissions([]);
            }
        }

        // Get all permissions grouped by category for response
        $allPermissions = \Spatie\Permission\Models\Permission::all()
            ->groupBy('kategori')
            ->mapWithKeys(function ($items, $key) {
                return [$key => $items->sortBy('order')->values()];
            })
            ->sortKeys();

        // Get updated role permissions grouped
        $rolePermissions = $role->permissions->groupBy('kategori');

        return response()->json([
            'message' => 'Role updated successfully',
            'data' => $role->load('permissions'),
            'available_permissions' => $allPermissions,
            'permissions_grouped' => $allPermissions,
            'role_permissions_grouped' => $rolePermissions,
            'changes' => [
                'name_changed' => $oldName !== $role->name,
                'permissions_updated' => $request->has('permissions'),
                'total_permissions' => $role->permissions->count(),
            ]
        ]);
    }

    public function indexPermissions()
    {
        $permissions = Permission::orderBy('kategori')->orderBy('order')->get();
        
        // Group permissions by category untuk checklist UI
        $groupedPermissions = $permissions->groupBy('kategori');
        
        return response()->json([
            'permissions' => $permissions,
            'grouped_permissions' => $groupedPermissions
        ]);
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:permissions',
            'kategori' => 'nullable|string',
            'order' => 'nullable|integer',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'guard_name' => 'api',
            'kategori' => $request->kategori ?? 'general',
            'order' => $request->order ?? 100,
        ]);

        return response()->json([
            'message' => 'Permission created successfully',
            'data' => $permission
        ], 201);
    }

    public function assignPermissionToRole(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Prevent assigning to super-admin role
        if ($role->name === 'super-admin') {
            return response()->json(['message' => 'Cannot modify super-admin role permissions'], 403);
        }

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return response()->json([
            'message' => 'Permissions assigned to role successfully',
            'data' => $role->load('permissions')
        ]);
    }

    public function assignPermissionsByAction(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'actions' => 'required|array',
            'actions.*' => 'in:store,update,delete',
            'categories' => 'nullable|array',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        if ($role->name === 'super-admin') {
            return response()->json(['message' => 'Cannot modify super-admin role permissions'], 403);
        }

        $query = Permission::query();
        
        // Filter by categories if provided
        if ($request->has('categories')) {
            $query->whereIn('kategori', $request->categories);
        }
        
        // Filter by actions
        $query->where(function($q) use ($request) {
            foreach ($request->actions as $action) {
                $q->orWhere('name', 'like', "%.$action");
            }
        });

        $permissions = $query->get();
        $role->syncPermissions($permissions);

        return response()->json([
            'message' => 'Permissions assigned by actions successfully',
            'actions' => $request->actions,
            'categories' => $request->categories ?? 'all',
            'permissions_count' => $permissions->count(),
            'data' => $role->load('permissions')
        ]);
    }

    public function getRoleWithPermissions()
    {
        $roles = Role::with('permissions')
            ->where('name', '!=', 'super-admin')
            ->get();

        // Transform data untuk checklist UI
        $roles->transform(function ($role) {
            $role->permissions_grouped = $role->permissions->groupBy('kategori');
            $role->permission_ids = $role->permissions->pluck('id')->toArray();
            return $role;
        });

        return response()->json($roles);
    }

    public function deleteRole(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found'], 404);
        }

        // Prevent deleting protected roles
        if (in_array($role->name, ['super-admin', 'Administrator'])) {
            return response()->json(['message' => 'Cannot delete protected role'], 403);
        }

        // Check if role is assigned to any users
        $usersCount = $role->users()->count();
        if ($usersCount > 0) {
            return response()->json([
                'message' => "Cannot delete role. It is assigned to {$usersCount} user(s). Please remove role from users first."
            ], 422);
        }

        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }

    public function destroyPermission(string $id)
    {
        $permission = Permission::find($id);

        if (!$permission) {
            return response()->json(['message' => 'Permission not found'], 404);
        }

        // Check if permission is assigned to any roles
        $rolesCount = $permission->roles()->count();
        if ($rolesCount > 0) {
            return response()->json([
                'message' => "Cannot delete permission. It is assigned to {$rolesCount} role(s)"
            ], 422);
        }

        $permission->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }

    public function removeRoleFromUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);

        // Prevent removing super-admin role
        if ($request->role === 'super-admin') {
            return response()->json(['message' => 'Cannot remove super-admin role'], 403);
        }

        if (!$user->hasRole($request->role)) {
            return response()->json(['message' => 'User does not have this role'], 400);
        }

        $user->removeRole($request->role);
        
        return response()->json([
            'message' => 'Role removed from user successfully',
            'data' => $user->load('roles')
        ]);
    }

    public function getUserWithRoleAndPermission(string $id)
    {
        $user = User::with(['roles', 'permissions'])->findOrFail($id);
        
        $user->all_permissions = $user->getAllPermissions();
        $user->permission_names = $user->getAllPermissions()->pluck('name');
        $user->role_names = $user->roles->pluck('name');

        return response()->json($user);
    }

    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);

        // Prevent assigning super-admin role
        if ($request->role === 'super-admin') {
            return response()->json(['message' => 'Cannot assign super-admin role'], 403);
        }

        // Use role name directly for Laravel 12
        $user->assignRole($request->role);

        return response()->json([
            'message' => 'Role assigned to user successfully',
            'data' => $user->load('roles')
        ]);
    }

    // Method khusus untuk mendapatkan permissions dalam format checklist
    public function getPermissionsForRoleForm()
    {
        $permissions = Permission::orderBy('kategori')->orderBy('order')->get();
        
        $structured = [];
        foreach ($permissions as $permission) {
            $category = $permission->kategori ?? 'general';
            if (!isset($structured[$category])) {
                $structured[$category] = [];
            }
            $structured[$category][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'display_name' => $this->formatDisplayName($permission->name),
                'order' => $permission->order ?? 100
            ];
        }

        // Sort each category by order
        foreach ($structured as $category => $perms) {
            usort($structured[$category], function ($a, $b) {
                return $a['order'] <=> $b['order'];
            });
        }

        return response()->json($structured);
    }

    public function getUsersWithRoles()
    {
        $users = User::with('roles')->get();
        
        // Hide super admin users for security
        $users = $users->filter(function ($user) {
            return !$user->hasRole('super-admin');
        });

        return response()->json($users->values());
    }

    public function bulkAssignPermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Prevent modifying super-admin role
        if ($role->name === 'super-admin') {
            return response()->json(['message' => 'Cannot modify super-admin role permissions'], 403);
        }

        $permissions = Permission::whereIn('id', $request->permissions)->get();
        $role->syncPermissions($permissions);

        return response()->json([
            'message' => 'Permissions bulk assigned successfully',
            'data' => $role->load('permissions')
        ]);
    }

    private function formatDisplayName(string $permissionName): string
    {
        // Convert "acara.store" to "Acara - Store"
        $parts = explode('.', $permissionName);
        $formatted = [];
        
        foreach ($parts as $part) {
            $formatted[] = ucwords(str_replace(['-', '_'], ' ', $part));
        }
        
        return implode(' - ', $formatted);
    }
}