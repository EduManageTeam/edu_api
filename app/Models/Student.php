<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'gender',
        'date_of_birth',
        'phone',
        'address',
        'photo',
        'class_id',
        'parent_id',
        'status',
    ];

    // student belongs to parent
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Parent::class, "parent_id");
    }

    // Student belongs to a class
    public function studentClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, "class_id");
    }
}
