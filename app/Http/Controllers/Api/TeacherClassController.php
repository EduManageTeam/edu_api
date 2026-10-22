<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassRoomResource;
use App\Http\Resources\ClassRoomResourceCollection;
use App\Models\ClassRoom;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;

class TeacherClassController extends Controller
{
    /**
     * GET /api/teacher/classes
     * List the classes assigned to the logged in teacher.
     */
    public function index(Request $request)
    {
        $teacher = $this->currentTeacher($request);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher profile not found.',
                'status' => 403,
            ], 403);
        }

        $classes = ClassRoom::with('teacher')
            ->withCount('students')
            ->where('teacher_id', $teacher->id)
            ->orderBy('name')
            ->get();

        return new ClassRoomResourceCollection($classes);
    }

    /**
     * GET /api/teacher/classes/{id}
     * Get one of the classes assigned to the logged in teacher.
     */
    public function show(Request $request, string $id)
    {
        $teacher = $this->currentTeacher($request);

        if (!$teacher) {
            return response()->json([
                'message' => 'Teacher profile not found.',
                'status' => 403,
            ], 403);
        }

        $class = ClassRoom::with(['teacher', 'students'])
            ->where('teacher_id', $teacher->id)
            ->find($id);

        if (!$class) {
            return response()->json([
                'message' => 'Data Not Found',
                'status' => 404,
            ], 404);
        }

        return new ClassRoomResource($class);
    }

    /**
     * Resolve the teacher record linked to the authenticated user.
     */
    private function currentTeacher(Request $request): ?Teacher
    {
        $user = $request->user();

        if (!$user instanceof User) {
            return null;
        }

        return $user->teacher;
    }
}
