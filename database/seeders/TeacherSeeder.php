<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $teacherRoleId = Role::where('name', 'teacher')->value('id');

        $teacherUsers = User::where('role_id', $teacherRoleId)->limit(10)->get();

        if ($teacherUsers->isEmpty()) {
            return;
        }

        $genders = ['Male', 'Female'];
        $statuses = ['active', 'inactive'];

        foreach ($teacherUsers as $index => $user) {
            Teacher::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'gender' => $genders[$index % 2],
                    'phone' => '09' . rand(1000000, 9999999),
                    'email' => $user->email,
                    'address' => 'Street ' . rand(1, 100) . ', Phnom Penh, Cambodia',
                    'photo' => null,
                    'status' => $statuses[$index % 2],
                ]
            );
        }
    }
}
