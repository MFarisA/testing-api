<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    // public function __construct()
    // {
    //     // Pastikan semua method memerlukan autentikasi
    //     $this->middleware('auth:sanctum');
    // }

    /**
     * Get authenticated user.
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => true,
            'message' => 'User profile retrieved successfully',
            'data' => $request->user()
        ], 200);
    }

    /**
     * Get all users (Admin only).
     */
    public function index()
    {
        if (!Gate::allows('isAdmin')) {
            return response()->json(['message' => 'Access denied'], 403);
        }

        $users = User::all();
        return response()->json([
            'status' => true,
            'message' => 'Users retrieved successfully',
            'data' => $users
        ], 200);
    }

    /**
     * Store a new user.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Show a single user.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json([
            'status' => true,
            'message' => 'User retrieved successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Update user.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,'.$id,
            'password' => 'sometimes|string|min:8'
        ]);

        $user = User::findOrFail($id);
        $user->update(array_filter($validated));

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Delete user.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully'
        ], 200);
    }
}
