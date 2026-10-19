<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@eduschool.com'],
            [
                'u_id' => $this->generateUserId('AD'),
                'name' => 'System Admin',
                'phone' => '012345678',
                'password' => Hash::make('234567891'),
                'role_id' => 1,
            ]
        );

        // Teacher
        User::firstOrCreate(
            ['email' => 't.sokdany@eduschool.com'],
            [
                'u_id' => $this->generateUserId('TCH'),
                'name' => 'Sok Dany',
                'phone' => '097656768',
                'password' => Hash::make('Teacher@123'),
                'role_id' => 2,
            ]
        );

        // Parent
        User::firstOrCreate(
            ['email' => 'p.chansophea@eduschool.com'],
            [
                'u_id' => $this->generateUserId('PAR'),
                'name' => 'Chan Sophea',
                'phone' => '0962345678',
                'password' => Hash::make('Parent@123'),
                'role_id' => 3,
            ]
        );

        // Student
        User::firstOrCreate(
            ['email' => 's.studentone@eduschool.com'],
            [
                'u_id' => $this->generateUserId('STU'),
                'name' => 'Van Sopheak',
                'phone' => '0123456789',
                'password' => Hash::make('Student@123'),
                'role_id' => 4,
            ]
        );
    }

    private function generateUserId(string $prefix): string
    {
        do {
            $uId = $prefix . '-' . Str::upper(Str::random(6));
        } while (
            User::where('u_id', $uId)->exists()
        );

        return $uId;
    }
}
