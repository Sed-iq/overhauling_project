<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login user and create token.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'school_id' => $user->school_id,
                ],
                'token' => $token,
            ]
        ]);
    }

    /**
     * Logout user and revoke token.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout successful'
        ]);
    }

    /**
     * Get authenticated user profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load(['school:id,name']);

        $profile = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'school' => $user->school,
        ];

        // Add role-specific data
        if ($user->isStudent()) {
            $user->load(['student.class:id,name,grade_level']);
            $profile['student_data'] = $user->student;
        } elseif ($user->isTeacher()) {
            $user->load(['teacher.classAssignments.class:id,name,grade_level']);
            $profile['teacher_data'] = $user->teacher;
        }

        return response()->json([
            'data' => $profile
        ]);
    }
}