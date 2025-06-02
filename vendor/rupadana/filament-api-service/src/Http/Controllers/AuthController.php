<?php

namespace Rupadana\ApiService\Http\Controllers;

use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Requests\LoginRequest;

class AuthController extends Controller
{
    /**
     * Login
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        if (Auth::validate($request->validated())) {
            $user = Auth::getLastAttempted();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Login success.',
                    'token' => $user->createToken($request->header('User-Agent'), ['*'])->plainTextToken,
                    'user' => $user
                ],
                201
            );
        }

        return response()->json(
            [
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ],
            401
        );
    }

    /**
     * Register
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'], // Added password validation rules
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('Member'); // Assign default role

        // Create token and return response
        return response()->json(
            [
                'success' => true,
                'message' => 'Register success.',
                'token' => $user->createToken($request->header('User-Agent'), ['*'])->plainTextToken,
            ],
            201
        );
    }

    /**
     * Logout
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json(
            [
                'success' => true,
                'message' => 'Logout success.',
            ],
            200
        );
    }
}
