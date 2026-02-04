<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Teacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'user_id',
        'employee_id',
        'subject_specialization',
        'hire_date',
        'salary',
    ];

    protected $hidden = [
        'salary',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
            'salary' => 'decimal:2',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function classAssignments(): HasMany
    {
        return $this->hasMany(TeacherClassAssignment::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(ClassModel::class, 'teacher_class_assignments', 'teacher_id', 'class_id')
                    ->withPivot('subject')
                    ->withTimestamps();
    }
}