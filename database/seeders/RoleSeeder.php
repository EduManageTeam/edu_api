<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'description' => 'Full system access'],
            ['name' => 'teacher', 'description' => 'Teacher access'],
            ['name' => 'parent', 'description' => 'Parent access'],
            ['name' => 'student', 'description' => 'Student access'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                ['description' => $role['description']]
            );
        }
    }
}
