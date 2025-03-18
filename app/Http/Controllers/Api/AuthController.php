<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Laravel\Passport\RefreshToken;
use Laravel\Passport\Token;

class AuthController extends Controller
{
    /**
     * Register a new user.
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Passport menggunakan metode token berbasis OAuth2
        $token = $user->createToken('Personal Access Token')->accessToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    /**
     * Login user and return token.
     */
    public function login(Request $request)
    {
        // Validate the request...
        
        // Attempt to find the user
        $user = User::where('email', $request->email)->first();

        // Check if user exists and password is correct
        if ($user && Hash::check($request->password, $user->password)) {
            // Create a new token
            $token = $user->createToken('tvku')->accessToken;

            // Return the token
            return response()->json(['token' => $token], 200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }


    /**
     * Logout user (Revoke token).
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        // Hapus semua token aktif milik user
        Token::where('user_id', $user->id)->delete();
        RefreshToken::where('access_token_id', $user->id)->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
