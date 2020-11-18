<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Api\V1\Requests\LoginRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Username or password is incorrect'
            ], 401);
        }

        $user = auth('api')->user();

        return response()->json([
            'access_token' => $token,
            'data' => [
                'id' => $user->id,
                'email' => $user->email,
                'name' => $user->name
            ]
        ]);
    }
}
