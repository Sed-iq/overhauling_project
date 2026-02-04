<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeacherClassAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'subject',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}