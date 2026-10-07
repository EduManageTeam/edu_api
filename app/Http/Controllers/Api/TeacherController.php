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
    public function index()
    {
        $teachers = Teacher::all();

        return response()->json([
            'message' => 'Success',
            'status' => 200,
            'data' => $teachers,
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
