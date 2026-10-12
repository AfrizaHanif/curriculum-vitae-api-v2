<?php

namespace App\Http\Controllers\APIs;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Authenticate the user and generate a new API token.
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['sometimes', 'nullable', 'string', 'max:255'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials do not match our records.',
                'errors' => [
                    'auth' => ['failed'],
                ],
            ], 401);
        }

        $user->loadMissing('profile');
        $deviceName = $request->input('device_name', 'auth_token');

        // Prune previous tokens with the same device name to avoid token accumulation
        $user->tokens()->where('name', $deviceName)->delete();

        return response()->json([
            'status' => 'success',
            'token' => $user->createToken($deviceName)->plainTextToken,
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Revoke the current access token for the authenticated user.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $currentToken = $user->currentAccessToken();

            // Guard against TransientToken instances during stateful requests or unit tests
            if ($currentToken && method_exists($currentToken, 'delete')) {
                $currentToken->delete();
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully. Token revoked.',
        ]);
    }
}
