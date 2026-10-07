<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassRoom extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name',
        'grade',
        'room',
        'teacher_id',
    ];

    protected $casts = [
        'teacher_id' => 'integer',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Teacher::class, 'teacher_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(\App\Models\Student::class);
    }
}
