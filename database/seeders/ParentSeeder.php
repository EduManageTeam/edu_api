<?php

namespace Database\Seeders;

use App\Models\ParentModel;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class ParentSeeder extends Seeder
{
    public function run(): void
    {
        $parentRoleId = Role::where('name', 'parent')->value('id');

        $parentUsers = User::where('role_id', $parentRoleId)->limit(10)->get();

        if ($parentUsers->isEmpty()) {
            return;
        }

        $genders = ['Male', 'Female'];

        foreach ($parentUsers as $index => $user) {
            ParentModel::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => $user->name,
                    'gender' => $genders[$index % 2],
                    'phone' => '09' . rand(1000000, 9999999),
                    'email' => $user->email,
                    'address' => 'Street ' . rand(1, 100) . ', Phnom Penh, Cambodia',
                    'photo' => null,
                ]
            );
        }
    }
}
