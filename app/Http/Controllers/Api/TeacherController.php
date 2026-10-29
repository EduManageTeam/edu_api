<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeacherController extends Controller
{
    /**
     * GET /api/teachers
     * Get all teachers.
     */

    public function index(Request $request)
    {
        // VALIDATE SEARCH AND FILTER
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'status' => 'nullable|in:active,inactive',
        ]);

        $query = Teacher::query();

        // SEARCH TEACHERS
        if (!empty($validated['search'])) {
            $search = trim($validated['search']);

            $query->where(function ($q) use ($search) {

                // search by name
                $q->whereRaw(
                    'LOWER(name) LIKE ?',
                    ['%' . strtolower($search) . '%']
                );

                // search phone number
                $q->orWhere('phone', 'LIKE', "%{$search}%");

                // Search by database ID
                if (ctype_digit($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }



        // filter teacher
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // get all matching teeacher
        $teachers = $query
            ->orderBy('id', 'desc')
            ->get();

        // count all teacher
        // COUNT MATCHING TEACHERS (SEARCH + FILTER)
        $stats = [
            'assigned' => $teachers->count(),

            'on_duty' => $teachers
                ->where('status', 'active')
                ->count(),

            'on_leave' => $teachers
                ->where('status', 'inactive')
                ->count(),
        ];

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'data' => $teachers,
            'stats' => $stats,
        ], 200);
    }


    /**
     * POST /api/teachers
     * Create a new teacher.
     */

    /**
     * POST /api/teachers
     * Create user account and teacher.
     */
    public function store(Request $request)
    {
        // Validate teacher information
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:100',
            'gender' => [
                'required',
                Rule::in(['male', 'female', 'other']),
            ],
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150|unique:users,email',
            'address' => 'nullable|string',
            'photo' => 'nullable|string|max:255',
            'status' => [
                'sometimes',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        // Find Teacher role
        $role = Role::whereRaw(
            'LOWER(name) = ?',
            ['teacher']
        )->first();

        if (!$role) {
            return response()->json([
                'message' => 'Teacher role not found',
                'status' => 404,
            ], 404);
        }

        // Create user and teacher together
        $teacher = DB::transaction(function () use ($validated, $role) {

            // Create user account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make('teacher@123'),
                'role_id' => $role->id,
            ]);
    
            // Create teacher linked to the user
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'photo' => $validated['photo'] ?? null,
                'status' => $validated['status'] ?? 'active',
            ]);

            return $teacher;
        });

        // Load generated login ID
        $teacher->load('user:id,u_id');

        return response()->json([
            'message' => 'Created Successfully',
            'status' => 201,
            'data' => $teacher,
            'login_id' => $teacher->user?->u_id,
        ], 201);
    }


    /**
     * GET /api/teachers/{id}
     * Get one teacher by ID.
     */
    public function show(string $id)
    {
        // Get teacher with user and assigned classes
        $teacher = Teacher::with([
            'user:id,u_id',

            'classes' => function ($query) {
                $query
                    ->withCount('students')
                    ->orderBy('grade')
                    ->orderBy('name');
            },
        ])->find($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        // Assigned classes
        $classes = $teacher->classes->map(function ($class) {
            return [
                'id' => $class->id,
                'name' => $class->name,
                'grade' => $class->grade,
                'room' => $class->room,
                'students_count' => (int) $class->students_count,
            ];
        })->values();

        // Teacher profile response
        $data = [
            'id' => $teacher->id,
            'user_id' => $teacher->user_id,
            'login_id' => $teacher->user?->u_id,
            'name' => $teacher->name,
            'gender' => $teacher->gender,
            'phone' => $teacher->phone,
            'email' => $teacher->email,
            'address' => $teacher->address,
            'photo' => $teacher->photo,
            'status' => $teacher->status,
            'joined_at' => $teacher->created_at,
            'classes_count' => $classes->count(),
            'students_count' => (int) $classes->sum(
                'students_count'
            ),
            'classes' => $classes,
        ];

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'data' => $data,
        ], 200);
    }

    /**
     * PUT /api/teachers/{id}
     * Update a teacher.
     */
    public function update(Request $request, string $id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        // Validate editable fields only
        $validated = $request->validate([
            'name' => 'sometimes|required|string|min:2|max:100',
            'gender' => [
                'sometimes',
                'required',
                Rule::in(['male', 'female', 'other']),
            ],
            'phone' => 'sometimes|nullable|string|max:20',
            'email' => 'sometimes|nullable|email|max:150',
            'address' => 'sometimes|nullable|string',
            'status' => [
                'sometimes',
                'required',
                Rule::in(['active', 'inactive']),
            ],
        ]);

        // Update teacher
        $teacher->update($validated);

        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 200,
            'data' => $teacher->fresh(),
        ], 200);
    }

    /**
     * PATCH /api/teachers/{id}/reset-password
     * Admin resets a teacher password.
     */
    public function resetPassword(Request $request, string $id)
    {
        // Validate new password
        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'max:255',
                'confirmed',
            ],
        ]);

        // Find teacher and related user account
        $teacher = Teacher::with('user')->find($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher Not Found',
                'status' => 404,
            ], 404);
        }

        if (!$teacher->user) {
            return response()->json([
                'message' => 'Teacher user account not found',
                'status' => 404,
            ], 404);
        }

        // Update password and revoke teacher tokens
        DB::transaction(function () use ($teacher, $validated) {
            $user = $teacher->user;

            $user->password = Hash::make(
                $validated['password']
            );

            $user->save();

            // Require teacher to log in again
            $user->tokens()->delete();
        });

        return response()->json([
            'message' => 'Teacher password reset successfully',
            'status' => 200,
        ], 200);
    }

    /**
     * DELETE /api/teachers/{id}
     * Delete a teacher.
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        $teacher->delete();

        return response()->json([
            'message' => 'Deleted Successfully',
            'status' => 200,
            'data' => null,
        ], 200);
    }
}
