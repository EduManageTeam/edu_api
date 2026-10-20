<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\Teacher;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $teachers = Teacher::pluck('id')->toArray();

        if (empty($teachers)) {
            return;
        }

        $grades = [
            'Grade 1', 'Grade 2', 'Grade 3', 'Grade 4', 'Grade 5',
            'Grade 6', 'Grade 7', 'Grade 8', 'Grade 9', 'Grade 10',
        ];
        $rooms = [
            'A-101', 'A-102', 'A-103', 'A-104', 'A-105',
            'B-101', 'B-102', 'B-103', 'B-104', 'B-105',
        ];

        foreach ($grades as $index => $grade) {
            ClassRoom::firstOrCreate(
                ['name' => $grade . ' Section A'],
                [
                    'grade' => $grade,
                    'room' => $rooms[$index % 10],
                    'teacher_id' => $teachers[array_rand($teachers)],
                ]
            );
        }
    }
}
