<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'school_id',
        'name',
        'grade_level',
        'capacity',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function teacherAssignments(): HasMany
    {
        return $this->hasMany(TeacherClassAssignment::class, 'class_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class, 'teacher_class_assignments', 'class_id', 'teacher_id')
                    ->withPivot('subject')
                    ->withTimestamps();
    }
}