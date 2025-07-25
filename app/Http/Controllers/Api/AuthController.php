<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\sitba; // <-- Panggil model Anda
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Register a new user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string|max:255|unique:sitba',
            'password' => 'required|string',
        ]);

        // Buat user baru
        $user = sitba::create([
            'username' => $request->username,
            'password' => Hash::make($request->password), // Enkripsi password
        ]);

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    /**
     * Authenticate user and return a token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Coba autentikasi user
        if (!Auth::guard('web')->attempt($request->only('username', 'password'))) {
             return response()->json([
                'message' => 'Invalid login details'
            ], 401);
        }

        // Dapatkan user yang berhasil login
        $user = sitba::where('username', $request['username'])->firstOrFail();

        // Buat token untuk user
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }
    
    /**
     * Log the user out (Invalidate the token).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
}