<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use Illuminate\Http\Request;

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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'name' => 'required|string|min:2|max:100',
            'gender' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'photo' => 'nullable|string|max:255',
            'status' => 'sometimes|string|max:30',
        ]);

        $teacher = Teacher::create($validated);

        return response()->json([
            'message' => 'Created Successfully',
            'status' => 201,
            'data' => $teacher,
        ], 201);
    }

    /**
     * GET /api/teachers/{id}
     * Get one teacher by ID.
     */
    public function show(string $id)
    {
        $teacher = Teacher::find($id);

        if (!$teacher) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'data' => $teacher,
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

        $validated = $request->validate([
            'user_id' => 'sometimes|required|integer|exists:users,id',
            'name' => 'sometimes|required|string|min:2|max:100',
            'gender' => 'sometimes|required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'address' => 'nullable|string',
            'photo' => 'nullable|string|max:255',
            'status' => 'sometimes|string|max:30',
        ]);

        $teacher->update($validated);

        return response()->json([
            'message' => 'Updated Successfully',
            'status' => 200,
            'data' => $teacher,
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
