<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    /**
     * Determine whether the user can view any students.
     */
    public function viewAny(User $user): bool
    {
        return $user->isSchoolAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can view the student.
     */
    public function view(User $user, Student $student): bool
    {
        // Users can only view students from their own school
        if ($user->school_id !== $student->school_id) {
            return false;
        }

        // School admins and teachers can view any student in their school
        if ($user->isSchoolAdmin() || $user->isTeacher()) {
            return true;
        }

        // Students can only view their own profile
        if ($user->isStudent()) {
            return $user->student && $user->student->id === $student->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create students.
     */
    public function create(User $user): bool
    {
        return $user->isSchoolAdmin() || $user->isTeacher();
    }

    /**
     * Determine whether the user can update the student.
     */
    public function update(User $user, Student $student): bool
    {
        // Users can only update students from their own school
        if ($user->school_id !== $student->school_id) {
            return false;
        }

        // School admins can update any student in their school
        if ($user->isSchoolAdmin()) {
            return true;
        }

        // Teachers can update students in their assigned classes
        if ($user->isTeacher() && $user->teacher) {
            $teacherClassIds = $user->teacher->classes->pluck('id')->toArray();
            return in_array($student->class_id, $teacherClassIds);
        }

        return false;
    }

    /**
     * Determine whether the user can delete the student.
     */
    public function delete(User $user, Student $student): bool
    {
        // Only school admins can delete students
        return $user->isSchoolAdmin() && $user->school_id === $student->school_id;
    }
}