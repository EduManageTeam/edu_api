<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'u_id',
        'name',
        'email',
        'phone',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'role_id' => 'integer',
    ];

    // Generate u_id automatically
    protected static function booted(): void
    {
        static::creating(function (User $user) {

            // Keep existing u_id if already provided
            if (!empty($user->u_id)) {
                return;
            }

            // Get user role
            $role = Role::findOrFail($user->role_id);

            // Generate ID based on role
            $user->u_id = self::generateUserId(
                strtolower(trim($role->name))
            );
        });
    }

    // Generate unique user ID
    public static function generateUserId(string $roleName): string
    {
        $prefix = match (strtolower(trim($roleName))) {
            'admin' => 'AD',
            'teacher' => 'TCH',
            'parent' => 'PAR',
            'student' => 'STU',
            default => throw new \InvalidArgumentException(
                'Invalid role'
            ),
        };

        do {
            $random = Str::upper(Str::random(6));
            $uId = $prefix . '-' . $random;
        } while (
            self::where('u_id', $uId)->exists()
        );

        return $uId;
    }

    // User belongs to a role
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
