<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Register a new user
     */
    public function register(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
            ]);
        } catch (ValidationException $e) {
            Log::error('Registration Validation Failed: ' . $e->getMessage());
            throw $e;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Registration successful'
        ], 201);
    }

    /**
     * Login user
     */
    public function login(Request $request)
    {
        Log::info('Login attempt:', $request->all());

        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            Log::error('Login Validation Failed: ' . $e->getMessage());
            Log::error('Validation errors:', $e->errors());
            throw $e;
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            Log::warning('Authentication failed for email: ' . $request->email);
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        Log::info('Login successful for user: ' . $user->id);

        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => 'Login successful'
        ]);
    }

    /**
     * Logout user
     */
    public function logout(Request $request)
    {
        $userId = $request->user()->id;
        Log::info('Logout attempt for user: ' . $userId);

        // Revoke the token that was used to authenticate the current request
        $request->user()->currentAccessToken()->delete();

        Log::info('Logout successful for user: ' . $userId);

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }
}
