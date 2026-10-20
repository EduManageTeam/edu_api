<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $students = Student::pluck('id')->toArray();
        $classes = ClassRoom::pluck('id')->toArray();

        if (empty($students) || empty($classes)) {
            return;
        }

        $statuses = ['present', 'absent', 'late', 'permission'];
        $remarks = ['', 'Sick leave', 'Family matter', 'Transport issue', null];

        $date = now()->subDays(9)->startOfDay();

        for ($i = 0; $i < 10; $i++) {
            Attendance::firstOrCreate(
                [
                    'student_id' => $students[array_rand($students)],
                    'class_id' => $classes[array_rand($classes)],
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
