<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'message' => ['Invalid email address or password sequence.'],
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'data' => Auth::user(),
        ], 200);
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // The User model hashes the password through its "hashed" cast
        $user = User::create($validated);

        Auth::login($user);
        $request->session()->regenerate();

        return response()->json([
            'data' => $user,
        ], 201);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user(),
        ], 200);
    }

    /**
     * Update the signed-in user's "About Me" profile.
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'skills' => ['nullable', 'array', 'max:15'],
            'skills.*' => ['string', 'max:30', 'distinct'],
        ]);

        $user = $request->user();
        $user->update($validated);

        return response()->json([
            'data' => $user->fresh(),
            'message' => 'Profile updated successfully.',
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Session successfully terminated.',
        ], 200);
    }
}
