<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherClassController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\StudentController;
use App\Models\User;
use Illuminate\Http\Request;
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
        Route::apiResource('classes', ClassController::class);
        Route::apiResource('subjects', SubjectController::class);

        // Lyhai - Teacher & Parent Management
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('parents', ParentController::class);

        // Delete only - Admin
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
        Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy']);

        Route::get('/admin/test', function () {
            return response()->json([
                'message' => 'Admin API Access'
            ]);
        });
    });

    // Admin and Teacher
    Route::middleware('admin.teacher')->group(function () {
        // Student
        Route::get('/students', [StudentController::class, 'index']);
        Route::post('/students', [StudentController::class, 'store']);
        Route::get('/students/{id}', [StudentController::class, 'show']);
        Route::put('/students/{id}', [StudentController::class, 'update']);

        // Attendance
        Route::get('/attendance', [AttendanceController::class, 'index']);
        Route::post('/attendance', [AttendanceController::class, 'store']);
        Route::get('/attendance/{id}', [AttendanceController::class, 'show']);
        Route::put('/attendance/{id}', [AttendanceController::class, 'update']);
    });

    // Teacher only
    Route::middleware('teacher')->group(function () {
        Route::get('/teacher/test', function () {
            return response()->json([
                'message' => 'Teacher API Access'
            ]);
        });

        // Teacher's own classes
        Route::get('/teacher/classes', [TeacherClassController::class, 'index']);
        Route::get('/teacher/classes/{id}', [TeacherClassController::class, 'show']);

        Route::get('/teacher/profile', function (Request $request) {

            $user = $request->user();

            if (!$user instanceof User) {
                return response()->json([
                    'message' => 'Unauthenticated'
                ], 401);
            }

            return response()->json([
                'user' => $user->load('role')
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
