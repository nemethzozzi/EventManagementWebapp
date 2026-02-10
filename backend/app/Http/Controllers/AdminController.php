<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Felhasználók listázása
     *
     * @return JsonResponse
     */
    public function listUsers()
    {
        $users = User::all();

        return response()->json($users);
    }

    /**
     * Új felhasználó létrehozása
     *
     * @return JsonResponse
     */
    public function createUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => 'required|in:user,helpdesk_agent,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_key' => 'admin_users_page.response.error.validation_failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return response()->json([
            'message_key' => 'admin_users_page.response.success.create_user',
            'user' => $user,
        ], 201);

    }

    /**
     * Felhasználó role módosítása
     *
     * @return JsonResponse
     */
    public function updateUserRole(Request $request, int $userId)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,helpdesk_agent,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($userId);
        $user->update(['role' => $request->role]);

        return response()->json($user);
    }

    /**
     * Felhasználó törlése
     *
     * @return JsonResponse
     */
    public function deleteUser(int $userId)
    {
        $user = User::findOrFail($userId);

        // Nem törölheti saját magát
        if ($user->id === auth('api')->id()) {
            return response()->json([
                'error_key' => 'admin_users_page.response.error.cannot_delete_urself',
            ], 400);
        }

        $user->delete();

        return response()->json([
            'message_key' => 'admin_users_page.response.success.delete_user',
        ]);
    }
}
