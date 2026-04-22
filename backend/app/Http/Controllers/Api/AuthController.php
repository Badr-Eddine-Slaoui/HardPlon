<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = Auth::guard('api')->attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            return response()->json([
                "success" => true,
                "data" => [
                    'access_token' => $token,
                    'token_type' => 'bearer',
                    'user' => Auth::guard('api')->user()
                ],
                "message" => "Logged in successfully."
            ],200);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Failed to login: " . $e->getMessage(),
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function me()
    {
        try {
            $user = Auth::guard('api')->user();

            return response()->json([
                "success" => true,
                "data" => compact('user'),
                "message" => "Successfully fetched user data."
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Failed to fetch user data: " . $e->getMessage(),
                "code" => $e->getCode()
            ], 500);
        }
    }

    public function logout()
    {
        try {
            Auth::guard('api')->logout();

            return response()->json([
                "success" => true,
                "data" => null,
                "message" => "Logged out successfully."
            ], 200);

        } catch (\Throwable $e) {
            return response()->json([
                "success" => false,
                "data" => null,
                "message" => "Failed to logout: " . $e->getMessage(),
                "code" => $e->getCode()
            ], 500);
        }
    }
}
