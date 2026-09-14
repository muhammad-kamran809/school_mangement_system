<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
            ],

            'password' => [
                'required',
                'string',
            ],

            'device_name' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $user = User::where(
            'email',
            $validated['email']
        )->first();

        if (
            ! $user ||
            ! Hash::check(
                $validated['password'],
                $user->password
            )
        ) {
            throw ValidationException::withMessages([
                'email' => [
                    'The provided credentials are incorrect.',
                ],
            ]);
        }

        $deviceName = $validated['device_name']
            ?? 'school-management';

        $token = $user
            ->createToken($deviceName)
            ->plainTextToken;

        return response()->json([
            'message' => 'Login successful.',

            'token' => $token,

            'token_type' => 'Bearer',

            'user' => [
                'id' => $user->id,

                'name' => $user->name,

                'email' => $user->email,

                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Current User
    |--------------------------------------------------------------------------
    */

    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'message' => 'Authenticated user retrieved successfully.',

            'user' => [
                'id' => $user->id,

                'name' => $user->name,

                'email' => $user->email,

                'roles' => $user->getRoleNames(),

                'permissions' => $user->getAllPermissions()
                    ->pluck('name')
                    ->values(),
            ],
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ?->delete();

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }
}
