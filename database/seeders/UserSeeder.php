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
        $adminId = \App\Models\Role::where('name', 'admin')->value('id') ?? 1;
        $teacherId = \App\Models\Role::where('name', 'teacher')->value('id') ?? 2;
        $parentId = \App\Models\Role::where('name', 'parent')->value('id') ?? 3;
        $studentId = \App\Models\Role::where('name', 'student')->value('id') ?? 4;

        // Admin (1)
        User::firstOrCreate(
            ['email' => 'admin@eduschool.com'],
            [
                'u_id' => $this->generateUserId('AD'),
                'name' => 'System Admin',
                'phone' => '012345678',
                'password' => Hash::make('234567891'),
                'role_id' => $adminId,
            ]
        );

        // Teachers (10)
        $teachers = [
            ['email' => 't.sokdany@eduschool.com', 'name' => 'Sok Dany', 'phone' => '097656768'],
            ['email' => 't.sopheap@eduschool.com', 'name' => 'Sopheap Khema', 'phone' => '097123456'],
            ['email' => 't.vireak@eduschool.com', 'name' => 'Vireak Chan', 'phone' => '097234567'],
            ['email' => 't.sreynich@eduschool.com', 'name' => 'Sreynich Mey', 'phone' => '097345678'],
            ['email' => 't.dara@eduschool.com', 'name' => 'Dara Phou', 'phone' => '097456789'],
            ['email' => 't.mony@eduschool.com', 'name' => 'Mony Roth', 'phone' => '097567890'],
            ['email' => 't.leakhena@eduschool.com', 'name' => 'Leakhena Ouk', 'phone' => '097678901'],
            ['email' => 't.samnang@eduschool.com', 'name' => 'Samnang Huot', 'phone' => '097789012'],
            ['email' => 't.pisey@eduschool.com', 'name' => 'Pisey Touch', 'phone' => '097890123'],
            ['email' => 't.rithy@eduschool.com', 'name' => 'Rithy Sao', 'phone' => '097901234'],
        ];

        foreach ($teachers as $teacher) {
            User::firstOrCreate(
                ['email' => $teacher['email']],
                [
                    'u_id' => $this->generateUserId('TCH'),
                    'name' => $teacher['name'],
                    'phone' => $teacher['phone'],
                    'password' => Hash::make('Teacher@123'),
                    'role_id' => $teacherId,
                ]
            );
        }

        // Parents (10)
        $parents = [
            ['email' => 'p.chansophea@eduschool.com', 'name' => 'Chan Sophea', 'phone' => '0962345678'],
            ['email' => 'p.sreymom@eduschool.com', 'name' => 'Sreymom Lim', 'phone' => '0963456789'],
            ['email' => 'p.bunthan@eduschool.com', 'name' => 'Bunthan Keo', 'phone' => '0964567890'],
            ['email' => 'p.sovan@eduschool.com', 'name' => 'Sovan Ngeth', 'phone' => '0965678901'],
            ['email' => 'p.maly@eduschool.com', 'name' => 'Maly Pech', 'phone' => '0966789012'],
            ['email' => 'p.ponlok@eduschool.com', 'name' => 'Ponlok Sea', 'phone' => '0967890123'],
            ['email' => 'p.sitha@eduschool.com', 'name' => 'Sitha Ung', 'phone' => '0968901234'],
            ['email' => 'p.chanthou@eduschool.com', 'name' => 'Chanthou Meas', 'phone' => '0969012345'],
            ['email' => 'p.dany@eduschool.com', 'name' => 'Dany Yin', 'phone' => '0970123456'],
            ['email' => 'p.ratana@eduschool.com', 'name' => 'Ratana Khut', 'phone' => '0971234567'],
        ];

        foreach ($parents as $parent) {
            User::firstOrCreate(
                ['email' => $parent['email']],
                [
                    'u_id' => $this->generateUserId('PAR'),
                    'name' => $parent['name'],
                    'phone' => $parent['phone'],
                    'password' => Hash::make('Parent@123'),
                    'role_id' => $parentId,
                ]
            );
        }

        // Students (10)
        $students = [
            ['email' => 's.studentone@eduschool.com', 'name' => 'Van Sopheak', 'phone' => '0123456789'],
            ['email' => 's.sreynich@eduschool.com', 'name' => 'Srey Nich', 'phone' => '0124567890'],
            ['email' => 's.rachana@eduschool.com', 'name' => 'Rachana Kim', 'phone' => '0125678901'],
            ['email' => 's.sovanna@eduschool.com', 'name' => 'Sovanna Pen', 'phone' => '0126789012'],
            ['email' => 's.chanty@eduschool.com', 'name' => 'Chanty Lam', 'phone' => '0127890123'],
            ['email' => 's.thida@eduschool.com', 'name' => 'Thida Heng', 'phone' => '0128901234'],
            ['email' => 's.mengchhea@eduschool.com', 'name' => 'Meng Chhea', 'phone' => '0129012345'],
            ['email' => 's.piseth@eduschool.com', 'name' => 'Piseth Som', 'phone' => '0130123456'],
            ['email' => 's.reaksmey@eduschool.com', 'name' => 'Reaksmey Yem', 'phone' => '0131234567'],
            ['email' => 's.sithanuon@eduschool.com', 'name' => 'Sitha Nuon', 'phone' => '0132345678'],
        ];

        foreach ($students as $student) {
            User::firstOrCreate(
                ['email' => $student['email']],
                [
                    'u_id' => $this->generateUserId('STU'),
                    'name' => $student['name'],
                    'phone' => $student['phone'],
                    'password' => Hash::make('Student@123'),
                    'role_id' => $studentId,
                ]
            );
        }
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
