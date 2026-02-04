<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;

class TeacherPolicy
{
    /**
     * Determine whether the user can assign classes to the teacher.
     */
    public function assignClass(User $user, Teacher $teacher): bool
    {
        // Only school admins can assign classes
        return $user->isSchoolAdmin() && $user->school_id === $teacher->school_id;
    }

    /**
     * Determine whether the user can view teacher's assigned classes.
     */
    public function viewAssignedClasses(User $user, Teacher $teacher): bool
    {
        // Users can only view teachers from their own school
        if ($user->school_id !== $teacher->school_id) {
            return false;
        }

        // School admins can view any teacher's assignments
        if ($user->isSchoolAdmin()) {
            return true;
        }

        // Teachers can view their own assignments
        if ($user->isTeacher() && $user->teacher) {
            return $user->teacher->id === $teacher->id;
        }

        return false;
    }
}