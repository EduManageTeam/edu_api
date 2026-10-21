<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::all();

        if ($students->isEmpty()) {
            return;
        }

        $statuses = ['present', 'absent', 'late', 'permission'];
        $remarks = ['', 'Sick leave', 'Family matter', 'Transport issue', null];

        $date = now()->subDays(9)->startOfDay();

        for ($i = 0; $i < 10; $i++) {
            $student = $students->random();

            Attendance::firstOrCreate(
                [
                    'student_id' => $student->id,
                    'class_id' => $student->class_id,
                    'date' => $date->copy()->addDays($i)->format('Y-m-d'),
                ],
                [
                    'status' => $statuses[array_rand($statuses)],
                    'remark' => $remarks[array_rand($remarks)],
                ]
            );
        }
    }
}
