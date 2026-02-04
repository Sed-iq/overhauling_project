<?php

namespace Database\Seeders;

use App\Models\ClassModel;
use App\Models\School;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherClassAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create schools
        $school1 = School::create([
            'name' => 'Greenwood Elementary',
            'address' => '123 Main St, Springfield',
            'phone' => '+1-555-0123',
            'email' => 'admin@greenwood.edu',
        ]);

        $school2 = School::create([
            'name' => 'Riverside High School',
            'address' => '456 Oak Ave, Springfield',
            'phone' => '+1-555-0456',
            'email' => 'admin@riverside.edu',
        ]);

        // Create school admins
        $admin1 = User::create([
            'school_id' => $school1->id,
            'name' => 'John Admin',
            'email' => 'admin@greenwood.edu',
            'password' => Hash::make('password123'),
            'role' => 'school_admin',
        ]);

        $admin2 = User::create([
            'school_id' => $school2->id,
            'name' => 'Jane Admin',
            'email' => 'admin@riverside.edu',
            'password' => Hash::make('password123'),
            'role' => 'school_admin',
        ]);

        // Create classes for school 1
        $class1A = ClassModel::create([
            'school_id' => $school1->id,
            'name' => 'Grade 1A',
            'grade_level' => '1',
            'capacity' => 25,
        ]);

        $class1B = ClassModel::create([
            'school_id' => $school1->id,
            'name' => 'Grade 1B',
            'grade_level' => '1',
            'capacity' => 25,
        ]);

        // Create classes for school 2
        $class9A = ClassModel::create([
            'school_id' => $school2->id,
            'name' => 'Grade 9A',
            'grade_level' => '9',
            'capacity' => 30,
        ]);

        // Create teachers
        $teacher1User = User::create([
            'school_id' => $school1->id,
            'name' => 'Mary Teacher',
            'email' => 'mary@greenwood.edu',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        $teacher1 = Teacher::create([
            'school_id' => $school1->id,
            'user_id' => $teacher1User->id,
            'employee_id' => 'T001',
            'subject_specialization' => 'Mathematics',
            'hire_date' => '2023-01-15',
            'salary' => 45000.00,
        ]);

        $teacher2User = User::create([
            'school_id' => $school2->id,
            'name' => 'Bob Teacher',
            'email' => 'bob@riverside.edu',
            'password' => Hash::make('password123'),
            'role' => 'teacher',
        ]);

        $teacher2 = Teacher::create([
            'school_id' => $school2->id,
            'user_id' => $teacher2User->id,
            'employee_id' => 'T002',
            'subject_specialization' => 'Science',
            'hire_date' => '2022-08-20',
            'salary' => 48000.00,
        ]);

        // Assign teachers to classes
        TeacherClassAssignment::create([
            'teacher_id' => $teacher1->id,
            'class_id' => $class1A->id,
            'subject' => 'Mathematics',
        ]);

        TeacherClassAssignment::create([
            'teacher_id' => $teacher1->id,
            'class_id' => $class1B->id,
            'subject' => 'Mathematics',
        ]);

        TeacherClassAssignment::create([
            'teacher_id' => $teacher2->id,
            'class_id' => $class9A->id,
            'subject' => 'Biology',
        ]);

        // Create students
        $student1User = User::create([
            'school_id' => $school1->id,
            'name' => 'Alice Student',
            'email' => 'alice@student.greenwood.edu',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        Student::create([
            'school_id' => $school1->id,
            'class_id' => $class1A->id,
            'user_id' => $student1User->id,
            'student_id' => 'S001',
            'date_of_birth' => '2017-05-15',
            'guardian_name' => 'Robert Smith',
            'guardian_phone' => '+1-555-1001',
            'address' => '789 Pine St, Springfield',
        ]);

        $student2User = User::create([
            'school_id' => $school2->id,
            'name' => 'Charlie Student',
            'email' => 'charlie@student.riverside.edu',
            'password' => Hash::make('password123'),
            'role' => 'student',
        ]);

        Student::create([
            'school_id' => $school2->id,
            'class_id' => $class9A->id,
            'user_id' => $student2User->id,
            'student_id' => 'S002',
            'date_of_birth' => '2009-03-22',
            'guardian_name' => 'Linda Johnson',
            'guardian_phone' => '+1-555-2002',
            'address' => '321 Elm St, Springfield',
        ]);
    }
}