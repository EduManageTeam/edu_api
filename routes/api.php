<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\ParentController;
use Illuminate\Support\Facades\Route;

// Public
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Admin only
    Route::middleware('admin')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);

        // Lyhai - Teacher & Parent Management
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('parents', ParentController::class);

        Route::get('/admin/test', function () {
            return response()->json([
                'message' => 'Admin API Access'
            ]);
        });
    });

    // Teacher only
    Route::middleware('teacher')->group(function () {
        Route::get('/teacher/test', function () {
            return response()->json([
                'message' => 'Teacher API Access'
            ]);
        });
    });

    // Parent only
    Route::middleware('parent')->group(function () {
        Route::get('/parent/test', function () {
            return response()->json([
                'message' => 'Parent API Access'
            ]);
        });
    });

});
