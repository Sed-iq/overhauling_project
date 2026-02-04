<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentationController;
use App\Http\Controllers\Api\StudentController;
use App\Http\Controllers\Api\TeacherController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/documentation', [DocumentationController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);

    // Student routes
    Route::middleware(['role:school_admin,teacher'])->group(function () {
        Route::apiResource('students', StudentController::class);
    });

    // Student profile route (students can view their own profile)
    Route::middleware(['role:student'])->group(function () {
        Route::get('/students/me', function () {
            $user = request()->user();
            $student = $user->student;
            
            if (!$student) {
                return response()->json(['message' => 'Student profile not found'], 404);
            }
            
            $student->load(['user:id,name,email', 'class:id,name,grade_level']);
            
            return response()->json(['data' => $student]);
        });
    });

    // Teacher routes
    Route::middleware(['role:school_admin'])->group(function () {
        Route::post('/teachers/{teacher}/assign-class', [TeacherController::class, 'assignClass']);
    });

    // Teacher can view their own assigned classes
    Route::middleware(['role:school_admin,teacher'])->group(function () {
        Route::get('/teachers/{teacher}/classes', [TeacherController::class, 'getAssignedClasses']);
    });
});