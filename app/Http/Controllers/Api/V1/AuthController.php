<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\User as UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Authenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController
{
    /**
     * Show current authenticated user
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function user(Request $request)
    {
        return UserResource::make($request->user())
            ->response();
    }

    /**
     * Login using username and password
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException
     */
    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string', 'alpha_dash', 'min:3', 'max:24'],
            'password' => ['required', 'string']
        ]);

        $user = User::where('username', '=', $data['username'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => 'Unable to find a user with the username provided',
            ]);
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Password provided does not match user',
            ]);
        }

        // If a user already has a valid token, just re-use back the
        // existing bearer token instead of regenerating a new one
        if($request->user('sanctum')) {
            $token = $request->bearerToken();
        }
        else {
            $token = $user->createToken('jwt')->plainTextToken;
        }

        return UserResource::make($user)
            ->additional([
                'token' => $token
            ])
            ->response();
    }
}
