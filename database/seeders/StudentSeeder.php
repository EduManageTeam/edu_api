<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use App\Models\ParentModel;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $classes = ClassRoom::pluck('id')->toArray();
        $parents = ParentModel::pluck('id')->toArray();

        if (empty($classes)) {
            return;
        }

        $genders = ['Male', 'Female'];
        $statuses = ['active', 'inactive'];
        $firstNames = ['Sopheak', 'Srey', 'Rachana', 'Sovanna', 'Chanty', 'Thida', 'Meng', 'Piseth', 'Reaksmey', 'Sitha'];
        $lastNames = ['Van', 'Kim', 'Pen', 'Lam', 'Heng', 'Chhea', 'Som', 'Yem', 'Nuon', 'Khema'];

        for ($i = 0; $i < 10; $i++) {
            Student::firstOrCreate(
                ['name' => $firstNames[$i] . ' ' . $lastNames[$i]],
                [
                    'gender' => $genders[$i % 2],
                    'date_of_birth' => '200' . rand(0, 9) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                    'phone' => '0' . rand(100000000, 999999999),
                    'address' => 'Street ' . rand(1, 100) . ', Phnom Penh, Cambodia',
                    'photo' => null,
                    'class_id' => $classes[array_rand($classes)],
                    'parent_id' => empty($parents) ? null : $parents[array_rand($parents)],
                    'status' => $statuses[$i % 2],
                ]
            );
        }
    }
}
