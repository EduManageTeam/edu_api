<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
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
            'name' => 'required|string|max:150',
            'gender' => 'required|string',
            'date_of_birth' => 'required|date',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'parent_id' => 'nullable|exists:parents,id',
            'status' => 'required|string',
            'photo' => 'nullable|string',
        ]);

        // Validate and normalize Base64 photo.
        $validated['photo'] = $this->validateBase64Photo(
            $validated['photo'] ?? null
        );

        $student = Student::create($validated);

        return response()->json([
            'message' => 'Student created successfully.',
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
            'name' => 'sometimes|required|string|max:150',
            'gender' => 'sometimes|required|string',
            'date_of_birth' => 'sometimes|required|date',
            'phone' => 'sometimes|required|string|max:20',
            'address' => 'sometimes|required|string',
            'class_id' => 'sometimes|required|exists:classes,id',
            'parent_id' => 'sometimes|nullable|exists:parents,id',
            'status' => 'sometimes|required|string',
            'photo' => 'sometimes|nullable|string',
        ]);

        // Only validate photo if the request contains it.
        if (array_key_exists('photo', $validated)) {
            $validated['photo'] = $this->validateBase64Photo(
                $validated['photo']
            );
        }

        $student->update($validated);

        return response()->json([
            'message' => 'Student updated successfully.',
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

    private function validateBase64Photo(?string $base64): ?string
    {
        if ($base64 === null || $base64 === '') {
            return null;
        }

        // Remove data URI prefix if present.
        if (str_contains($base64, ',')) {
            $base64 = explode(',', $base64, 2)[1];
        }

        $imageData = base64_decode($base64, true);

        if ($imageData === false) {
            throw ValidationException::withMessages([
                'photo' => ['Invalid Base64 image.'],
            ]);
        }

        // Maximum decoded image size: 2 MB.
        if (strlen($imageData) > 2 * 1024 * 1024) {
            throw ValidationException::withMessages([
                'photo' => ['Photo must not exceed 2 MB.'],
            ]);
        }

        $imageInfo = @getimagesizefromstring($imageData);

        if ($imageInfo === false) {
            throw ValidationException::withMessages([
                'photo' => ['Invalid image file.'],
            ]);
        }

        $allowedTypes = [
            'image/jpeg',
            'image/png',
            'image/webp',
        ];

        if (!in_array($imageInfo['mime'], $allowedTypes, true)) {
            throw ValidationException::withMessages([
                'photo' => ['Only JPG, PNG and WEBP images are allowed.'],
            ]);
        }

        // Return normalized Base64 for database storage.
        return base64_encode($imageData);
    }
}
