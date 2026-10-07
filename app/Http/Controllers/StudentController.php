<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index()
    {
        $students = Student::all();

        return response()->json([
            'message' => 'Students retrieved successfully.',
            'status' => 200,
            'data' => $students,
        ], 200);
    }

    /**
     * Store a newly created student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:2'],
            'gender' => ['required', 'string'],
            'date_of_birth' => ['nullable', 'date'],
            'phone' => ['required', 'string'],
            'address' => ['nullable', 'string'],
            'photo' => ['nullable', 'string'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'parent_id' => ['nullable', 'integer', 'exists:parents,id'],
            'status' => ['nullable', 'string'],
        ]);

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Student created successfully.',
            'status' => 201,
            'data' => $student,
        ], 201);
    }

    /**
     * Display the specified student.
     */
    public function show(int $id)
    {
        $student = Student::findOrFail($id);

        return response()->json([
            'message' => 'Student retrieved successfully.',
            'status' => 200,
            'data' => $student,
        ], 200);
    }

    /**
     * Update the specified student.
     */
    public function update(Request $request, int $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'min:2'],
            'gender' => ['sometimes', 'required', 'string'],
            'date_of_birth' => ['sometimes', 'nullable', 'date'],
            'phone' => ['sometimes', 'required', 'string'],
            'address' => ['sometimes', 'nullable', 'string'],
            'photo' => ['sometimes', 'nullable', 'string'],
            'class_id' => ['sometimes', 'required', 'integer', 'exists:classes,id'],
            'parent_id' => ['sometimes', 'nullable', 'integer', 'exists:parents,id'],
            'status' => ['sometimes', 'nullable', 'string'],
        ]);

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully.',
            'status' => 200,
            'data' => $student->fresh(),
        ], 200);
    }

    /**
     * Remove the specified student.
     */
    public function destroy(int $id)
    {
        $student = Student::findOrFail($id);

        $student->delete();

        return response()->json([
            'message' => 'Student deleted successfully.',
            'status' => 200,
            'data' => null,
        ], 200);
    }
}
