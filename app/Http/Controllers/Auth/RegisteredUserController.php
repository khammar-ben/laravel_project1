<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:admins,email'],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);

            // Generate username from email if not provided
            $username = explode('@', $request->email)[0];

            $admin = Admin::create([
                'username' => $username,
                'email' => $request->email,
                'full_name' => $request->name,
                'password' => Hash::make($request->string('password')),
                'role' => 'admin',
            ]);

            event(new Registered($admin));

            // Create token for API authentication (no session login needed for API)
            $token = $admin->createToken('auth-token')->plainTextToken;

            return response()->json([
                'user' => $admin,
                'token' => $token,
                'message' => 'Registration successful'
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
