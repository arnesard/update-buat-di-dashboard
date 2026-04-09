<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;

class ApiAuthController extends Controller
{
    /**
     * Login dan dapatkan API token.
     * POST /api/login
     * Body: { "name": "admin", "password": "password" }
     */
    public function login(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('name', $request->name)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Username atau password salah.'
            ], 401);
        }

        // Generate token baru
        $token = Str::random(64);
        $user->api_token = $token;
        $user->save();

        return response()->json([
            'message' => 'Login berhasil!',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ]);
    }

    /**
     * Logout dan hapus token.
     * POST /api/logout
     * Header: Authorization: Bearer {token}
     */
    public function logout(Request $request)
    {
        $user = auth()->user();
        $user->api_token = null;
        $user->save();

        return response()->json([
            'message' => 'Logout berhasil!'
        ]);
    }

    /**
     * Get user info.
     * GET /api/me
     * Header: Authorization: Bearer {token}
     */
    public function me(Request $request)
    {
        $user = auth()->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ]
        ]);
    }
}
