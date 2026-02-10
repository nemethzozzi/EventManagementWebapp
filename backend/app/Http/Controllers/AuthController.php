<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Login endpoint - JWT token generálás
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_key' => 'auth.response.error.validation_failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        try {
            $token = JWTAuth::attempt($credentials);
        } catch (\Throwable $e) {
            return response()->json([
                'error_key' => 'auth.response.error.invalid_credentials',
            ], 401);
        }

        if (! $token) {
            return response()->json([
                'error_key' => 'auth.response.error.invalid_credentials',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Aktuális user adatai
     */
    public function me(): JsonResponse
    {
        return response()->json([
            'data' => auth('api')->user(),
        ]);
    }

    /**
     * Logout - token invalidálása
     */
    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message_key' => 'auth.response.success.logout',
        ]);
    }

    /**
     * Token frissítése
     */
    public function refresh(): JsonResponse
    {
        $token = JWTAuth::parseToken()->refresh();

        return $this->respondWithToken($token, true);
    }

    /**
     * Token válasz formázása
     */
    protected function respondWithToken(string $token, bool $isRefresh = false): JsonResponse
    {
        return response()->json([
            'message_key' => $isRefresh ? 'auth.token_refreshed' : 'auth.login_success',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'user' => auth('api')->user(),
        ]);
    }
}
