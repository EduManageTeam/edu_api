<?php

namespace App\Http\Controllers;

use App\Models\attendance;
use App\Models\Attendance as ModelsAttendance;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::with([
            'student',
            'schoolClass'
        ])->get();

        return response()->json([
            'data' => $attendances,
            'total' => $attendances->count(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => ['required', 'integer',
                Rule::exists('students', 'id'),
            ],

            'class_id' => ['required', 'integer',
                Rule::exists('classes', 'id'),
            ],

            'date' => ['required','date',],

            'status' => ['required',
                Rule::in(['present', 'absent', 'late', 'permission',]),
            ],

            'remark' => ['nullable', 'string',],
        ]);

        $exists = Attendance::where('student_id', $validated['student_id'])
            ->where('class_id', $validated['class_id'])
            ->whereDate('date', $validated['date'])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Attendance already exists for this student on this date.',
            ], 409);
        }

        $attendance = Attendance::create($validated);

        return response()->json([
            'message' => 'Attendance created successfully',
            'data' => $attendance->load([
                'student',
                'schoolClass',
            ]),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        return response()->json([
            'data' => $attendance->load([
                'student',
                'schoolClass',
            ]),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
     public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'student_id' => ['sometimes', 'required', 'integer',
                Rule::exists('students', 'id'),
            ],
            'class_id' => ['sometimes', 'required','integer',
                Rule::exists('classes', 'id'),
            ],
            'date' => ['sometimes', 'required', 'date',],
            'status' => ['sometimes','required',
                Rule::in(['present', 'absent', 'late', 'permission',]),
            ],
            'remark' => ['nullable','string',],
        ]);

        $studentId = $validated['student_id'] ?? $attendance->student_id;
        $classId = $validated['class_id'] ?? $attendance->class_id;

        $date = $validated['date']
            ?? $attendance->date->format('Y-m-d');

        $duplicate = Attendance::where('student_id', $studentId)
            ->where('class_id', $classId)
            ->whereDate('date', $date)
            ->where('id', '!=', $attendance->id)
            ->exists();

        if ($duplicate) {
            return response()->json([
                'message' => 'Attendance already exists for this student on this date.',
            ], 409);
        }

        $attendance->update($validated);

        return response()->json([
            'message' => 'Attendance updated successfully',
            'data' => $attendance->load([
                'student',
                'schoolClass',
            ]),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(attendance $attendance)
    {
        $attendance->delete();
        return response()->json([
            'message' => 'Attendance deleted successfully',
        ]);
    }
}
