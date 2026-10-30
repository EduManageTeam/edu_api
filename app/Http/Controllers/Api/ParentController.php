<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParentModel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ParentController extends Controller
{


    /**
     * GET /api/parents
     * Get all parents with linked students.
     */
    public function index(Request $request)
    {
        // Validate search and filter
        $validated = $request->validate([
            'search' => 'nullable|string|max:100',
            'filter' => 'nullable|in:linked,unlinked',
        ]);

        $query = ParentModel::query();

        // Search parents
        if (!empty($validated['search'])) {
            $search = trim($validated['search']);

            $query->where(function ($q) use ($search) {
                // Search by name
                $q->whereRaw(
                    'LOWER(name) LIKE ?',
                    ['%' . strtolower($search) . '%']
                );

                // Search by phone
                $q->orWhere('phone', 'LIKE', "%{$search}%");

                // Search by database ID
                if (ctype_digit($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        // Filter parents
        if (!empty($validated['filter'])) {
            if ($validated['filter'] === 'linked') {
                $query->has('students');
            } elseif ($validated['filter'] === 'unlinked') {
                $query->doesntHave('students');
            }
        }

        // Get parent data
        $parents = $query
            ->with([
                'user:id,u_id',
                'students.studentClass',
            ])
            ->withCount('students')
            ->orderBy('id', 'desc')
            ->get();

        $data = $parents->map(function ($parent) {

            $children = $parent->students->map(function ($student) {
                $class = $student->studentClass;

                return [
                    'id' => $student->id,
                    'name' => $student->name,
                    'gender' => $student->gender,
                    'photo' => $student->photo,
                    'status' => $student->status,

                    'class' => $class ? [
                        'id' => $class->id,
                        'name' => $class->name,
                        'grade' => $class->grade,
                        'room' => $class->room,
                    ] : null,
                ];

            })->values();

            return [
                'id' => $parent->id,
                'user_id' => $parent->user_id,
                'login_id' => $parent->user?->u_id,
                'name' => $parent->name,
                'gender' => $parent->gender,
                'phone' => $parent->phone,
                'email' => $parent->email,
                'address' => $parent->address,
                'photo' => $parent->photo,
                'joined_at' => $parent->created_at,

                'children_count' => (int) $parent->students_count,

                'classes_count' => $parent->students
                    ->pluck('class_id')
                    ->filter(fn($id) => $id !== null)
                    ->unique()
                    ->count(),

                'children' => $children,
            ];
        })->values();

        // Count matching parents
        $stats = [
            'total' => $parents->count(),

            'linked' => $parents
                ->where('students_count', '>', 0)
                ->count(),

            'unlinked' => $parents
                ->where('students_count', 0)
                ->count(),
        ];

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'data' => $data,
            'stats' => $stats,
        ], 200);
    }

    /**
     * POST /api/parents
     * Create user account and parent together.
     */
    public function store(Request $request)
    {
        // Validate parent information
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
        ]);

        // Find Parent role
        $role = Role::whereRaw(
            'LOWER(name) = ?',
            ['parent']
        )->first();

        if (!$role) {
            return response()->json([
                'message' => 'Parent role not found',
                'status' => 404,
            ], 404);
        }

        // Create user account and parent together
        $parent = DB::transaction(function () use ($validated, $role) {

            // Create user account
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make('Parent@123'),
                'role_id' => $role->id,
            ]);

            // Create parent linked to the user
            $parent = ParentModel::create([
                'user_id' => $user->id,
                'name' => $validated['name'],
                'gender' => $validated['gender'],
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'photo' => $validated['photo'] ?? null,
            ]);

            return $parent;
        });

        // Load generated login ID
        $parent->load('user:id,u_id');

        return response()->json([
            'message' => 'Created Successfully',
            'status' => 201,
            'data' => $parent,
            'login_id' => $parent->user?->u_id,
        ], 201);
    }

    /**
     * GET /api/parents/{id}
     * Get one parent with linked children.
     */
    public function show(string $id)
    {
        // Get parent with user and linked students
        $parent = ParentModel::with([
            'user:id,u_id',
            'students.studentClass',
        ])
            ->withCount('students')
            ->find($id);

        if (!$parent) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        // Linked children
        $children = $parent->students->map(function ($student) {
            $class = $student->studentClass;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'gender' => $student->gender,
                'photo' => $student->photo,
                'status' => $student->status,

                'class' => $class ? [
                    'id' => $class->id,
                    'name' => $class->name,
                    'grade' => $class->grade,
                    'room' => $class->room,
                ] : null,
            ];
        })->values();

        // Parent profile response
        $data = [
            'id' => $parent->id,
            'user_id' => $parent->user_id,
            'login_id' => $parent->user?->u_id,
            'name' => $parent->name,
            'gender' => $parent->gender,
            'phone' => $parent->phone,
            'email' => $parent->email,
            'address' => $parent->address,
            'photo' => $parent->photo,
            'joined_at' => $parent->created_at,

            'children_count' => (int) $parent->students_count,

            'classes_count' => $parent->students
                ->pluck('class_id')
                ->filter(fn($id) =>  $id !== null)
                ->unique()
                ->count(),

            'children' => $children,
        ];

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'data' => $data,
        ], 200);
    }

    /**
     * PUT /api/parents/{id}
     * Update parent information only.
     */
    public function update(Request $request, string $id)
    {
        $parent = ParentModel::find($id);

        if (!$parent) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        // Validate editable parent information
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
            'photo' => 'sometimes|nullable|string|max:255',
        ]);

        $parent->update($validated);

        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 200,
            'data' => $parent->fresh(),
        ], 200);
    }

    /**
     * PATCH /api/parents/{id}/reset-password
     * Admin resets a parent password.
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

        // Find parent and related user account
        $parent = ParentModel::with('user')->find($id);

        if (!$parent) {
            return response()->json([
                'message' => 'Parent Not Found',
                'status' => 404,
            ], 404);
        }

        if (!$parent->user) {
            return response()->json([
                'message' => 'Parent user account not found',
                'status' => 404,
            ], 404);
        }

        // Update password and revoke parent tokens
        DB::transaction(function () use ($parent, $validated) {

            $user = $parent->user;

            $user->password = Hash::make(
                $validated['password']
            );

            $user->save();

            // Require parent to log in again
            $user->tokens()->delete();
        });

        return response()->json([
            'message' => 'Parent password reset successfully',
            'status' => 200,
        ], 200);
    }


    /**
     * DELETE /api/parents/{id}
     * Delete parent and linked user account.
     */
    public function destroy(string $id)
    {
        $parent = ParentModel::with('user')->find($id);

        if (!$parent) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        // Prevent deleting parent with linked students
        if ($parent->students()->exists()) {
            return response()->json([
                'message' => 'Cannot delete parent with linked students',
                'status' => 409,
            ], 409);
        }

        // Delete parent and user together
        DB::transaction(function () use ($parent) {
            $user = $parent->user;

            $parent->delete();

            if ($user) {
                $user->tokens()->delete();
                $user->delete();
            }
        });

        return response()->json([
            'message' => 'Deleted Successfully',
            'status' => 200,
            'data' => null,
        ], 200);
    }
}
