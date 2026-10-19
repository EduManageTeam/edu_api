
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Create u_id if it does not exist
        if (!Schema::hasColumn('users', 'u_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('u_id', 20)
                    ->nullable()
                    ->unique()
                    ->after('id');
            });
        }

        // 2. Get existing users without u_id
        $users = DB::table('users')
            ->leftJoin('roles', 'users.role_id', '=', 'roles.id')
            ->where(function ($query) {
                $query->whereNull('users.u_id')
                      ->orWhere('users.u_id', '');
            })
            ->select('users.id', 'roles.name as role_name')
            ->get();

        // 3. Generate User ID
        foreach ($users as $user) {

            $prefix = match (strtolower(trim($user->role_name ?? ''))) {
                'admin'   => 'AD',
                'teacher' => 'TCH',
                'student' => 'STU',
                'parent'  => 'PAR',
                default => throw new \RuntimeException(
                    "Invalid role for user ID: {$user->id}"
                ),
            };

            do {
                $uId = $prefix . '-' . Str::upper(Str::random(6));
            } while (
                DB::table('users')->where('u_id', $uId)->exists()
            );

            DB::table('users')
                ->where('id', $user->id)
                ->update(['u_id' => $uId]);
        }

        // 4. Check missing IDs
        if (DB::table('users')
            ->where(function ($query) {
                $query->whereNull('u_id')
                      ->orWhere('u_id', '');
            })
            ->exists()) {

            throw new \RuntimeException(
                'Some users do not have u_id.'
            );
        }

        // 5. Change u_id to NOT NULL
        Schema::table('users', function (Blueprint $table) {
            $table->string('u_id', 20)
                ->nullable(false)
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('u_id', 20)
                ->nullable()
                ->change();
        });
    }
};
