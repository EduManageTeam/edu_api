<?php

namespace Database\Seeders;

use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            ['name' => 'Mathematics', 'description' => 'Basic and advanced mathematics'],
            ['name' => 'Physics', 'description' => 'Physical sciences and mechanics'],
            ['name' => 'Chemistry', 'description' => 'Chemical sciences and reactions'],
            ['name' => 'Biology', 'description' => 'Life sciences and biology'],
            ['name' => 'English', 'description' => 'English language and literature'],
            ['name' => 'Khmer Literature', 'description' => 'Khmer language and literature'],
            ['name' => 'History', 'description' => 'World and national history'],
            ['name' => 'Geography', 'description' => 'World and regional geography'],
            ['name' => 'Computer Science', 'description' => 'Computing and information technology'],
            ['name' => 'Civics', 'description' => 'Citizenship and moral education'],
        ];

        foreach ($subjects as $subject) {
            Subject::firstOrCreate(
                ['name' => $subject['name']],
                $subject
            );
        }
    }
}
