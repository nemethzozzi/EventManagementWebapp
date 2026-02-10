<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Login endpoint - JWT token generálás
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_key' => 'auth.response.error.validation_failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'error_key' => 'auth.response.error.invalid_credentials',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Aktuális user adatai
     */
    public function me()
    {
        return response()->json([
            'data' => auth('api')->user(),
        ]);
    }

    /**
     * Logout - token invalidálása
     */
    public function logout()
    {
        auth('api')->logout();

        return response()->json([
            'message_key' => 'auth.response.success.logout',
        ]);
    }

    /**
     * Token frissítése
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh(), true);
    }

    /**
     * Token válasz formázása
     */
    protected function respondWithToken($token, bool $isRefresh = false)
    {
        return response()->json([
            'message_key' => $isRefresh ? 'auth.token_refreshed' : 'auth.login_success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }
}
