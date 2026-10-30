<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClassController;
use App\Http\Controllers\Api\ParentController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\TeacherController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\StudentController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


// Public Route
Route::post('/login', [AuthController::class, 'login']);


// Protected Routes
Route::middleware('auth:sanctum')->group(function () {

    // Authentication
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // ADMIN ONLY
    Route::middleware('admin')->group(function () {

        Route::post('/register', [AuthController::class, 'register']);

        // User Management
        Route::apiResource('users', UserController::class);
        Route::apiResource('roles', RoleController::class);

        // Academic Management
        Route::apiResource('classes', ClassController::class);
        Route::apiResource('subjects', SubjectController::class);

        // Teacher & Parent Management
        Route::apiResource('teachers', TeacherController::class);
        Route::apiResource('parents', ParentController::class);

        // Only Admin can delete
        Route::delete('/students/{id}', [StudentController::class, 'destroy']);
        Route::delete('/attendance/{id}', [AttendanceController::class, 'destroy']);

        // Admin can update students and attendance
        Route::patch('/students/{id}', [StudentController::class, 'update']);
        Route::patch('/attendance/{id}', [AttendanceController::class, 'update']);

        Route::get('/admin/test', function () {
            return response()->json([
                'message' => 'Admin API Access'
            ]);
        });

        // Only admin can reset teacher password
        Route::patch(
            'teachers/{teacher}/reset-password',
            [TeacherController::class, 'resetPassword']
        );

        // only admin can reset parent paasword
        Route::patch(
            'parents/{id}/reset-password',
            [ParentController::class, 'resetPassword']
        );
    });


    // ADMIN OR TEACHER
    Route::middleware('admin.or.teacher')->group(function () {

        // Students
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


    // TEACHER ONLY
    Route::middleware('teacher')->group(function () {

        Route::get('/teacher/test', function () {
            return response()->json([
                'message' => 'Teacher API Access'
            ]);
        });

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

    // PARENT ONLY
    Route::middleware('parent')->group(function () {
        Route::get('/parent/test', function () {
            return response()->json([
                'message' => 'Parent API Access'
            ]);
        });

        Route::get('/parent/profile', function (Request $request) {
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

    // STUDENT ONLY
    Route::middleware(['student'])->group(function () {
        Route::get('/student/test', function () {
            return response()->json([
                'message' => 'Student API Access'
            ]);
        });
        Route::get('/student/profile', function (Request $request) {
            $user = $request->user();
            return response()->json([
                'user' => $user
            ]);
        });
    });

});
