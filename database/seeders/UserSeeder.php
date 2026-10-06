<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@eduschool.com',
            'phone' => '012345678',
            'password' => Hash::make('234567891'),
            'role_id' => 1,
        ]);

        User::create([
            'name' => 'Sok Dany',
            'email' => 't.sokdany@eduschool.com',
            'phone' => '097656768',
            'password' => Hash::make('19191818'),
            'role_id' => 2,
        ]);

        User::create([
            'name' => 'Chan Sophea',
            'email' => 'p.chansophea@eduschool.com',
            'phone' => '0962345678',
            'password' => Hash::make('12345678'),
            'role_id' => 3,
        ]);
    }
}
