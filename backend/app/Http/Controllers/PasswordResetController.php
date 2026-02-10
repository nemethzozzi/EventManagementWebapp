<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Jelszó reset link küldése emailben
     *
     * @return JsonResponse
     */
    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Password reset link küldése
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'message_key' => 'forgot_password_page.response.success.email_sent',
            ]);
        }

        // Tipikus: user nem létezik / throttled / stb.
        return response()->json([
            'error_key' => $this->mapForgotStatusToErrorKey($status),
        ], 400);
    }

    /**
     * Jelszó reset végrehajtása
     *
     * @return JsonResponse
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json([
                'message_key' => 'reset_password_page.response.success.reset',
            ]);
        }

        return response()->json([
            'error_key' => $this->mapResetStatusToErrorKey($status),
        ], 400);
    }

    /**
     * Elfelejtett jelszó hibák
     */
    private function mapForgotStatusToErrorKey(string $status): string
    {
        return match ($status) {
            // Note: azért kell a "__()" rész mivel a nyelvi kulcs ellenőrző szkript csak így találja meg
            Password::INVALID_USER => __('forgot_password_page.response.error.invalid_user'),
            Password::RESET_THROTTLED => __('forgot_password_page.response.error.throttled'),
            default => __('forgot_password_page.response.error.failed'),
        };
    }

    /**
     * Új jelszó hibák
     */
    private function mapResetStatusToErrorKey(string $status): string
    {
        return match ($status) {
            // Note: azért kell a "__()" rész mivel a nyelvi kulcs ellenőrző szkript csak így találja meg
            Password::INVALID_TOKEN => __('reset_password_page.response.error.invalid_token'),
            Password::INVALID_USER => __('reset_password_page.response.error.invalid_user'),
            Password::RESET_THROTTLED => __('reset_password_page.response.error.throttled'),
            default => __('reset_password_page.response.error.failed'),
        };
    }
}
