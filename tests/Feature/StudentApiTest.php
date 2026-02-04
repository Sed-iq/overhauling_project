<?php

namespace Tests\Feature;

use App\Models\ClassModel;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StudentApiTest extends TestCase
{
    use RefreshDatabase;

    private School $school;
    private User $admin;
    private User $teacher;
    private ClassModel $class;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test school
        $this->school = School::create([
            'name' => 'Test School',
            'address' => '123 Test St',
            'phone' => '+1-555-0000',
            'email' => 'test@school.edu',
        ]);

        // Create admin user
        $this->admin = User::create([
            'school_id' => $this->school->id,
            'name' => 'Test Admin',
            'email' => 'admin@test.edu',
            'password' => Hash::make('password'),
            'role' => 'school_admin',
        ]);

        // Create teacher user
        $this->teacher = User::create([
            'school_id' => $this->school->id,
            'name' => 'Test Teacher',
            'email' => 'teacher@test.edu',
            'password' => Hash::make('password'),
            'role' => 'teacher',
        ]);

        // Create test class
        $this->class = ClassModel::create([
            'school_id' => $this->school->id,
            'name' => 'Test Class',
            'grade_level' => '1',
            'capacity' => 30,
        ]);
    }

    public function test_admin_can_create_student(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/students', [
                'name' => 'Test Student',
                'email' => 'student@test.edu',
                'password' => 'password123',
                'class_id' => $this->class->id,
                'student_id' => 'S001',
                'date_of_birth' => '2015-01-01',
                'guardian_name' => 'Test Guardian',
                'guardian_phone' => '+1-555-1111',
                'address' => '456 Test Ave',
            ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'data' => [
                        'id',
                        'student_id',
                        'date_of_birth',
                        'guardian_name',
                        'guardian_phone',
                        'address',
                        'user' => ['id', 'name', 'email'],
                        'class' => ['id', 'name', 'grade_level'],
                    ]
                ]);

        $this->assertDatabaseHas('students', [
            'student_id' => 'S001',
            'school_id' => $this->school->id,
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'student@test.edu',
            'role' => 'student',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_teacher_can_create_student(): void
    {
        $response = $this->actingAs($this->teacher, 'sanctum')
            ->postJson('/api/students', [
                'name' => 'Test Student 2',
                'email' => 'student2@test.edu',
                'password' => 'password123',
                'class_id' => $this->class->id,
                'student_id' => 'S002',
                'date_of_birth' => '2015-02-01',
                'guardian_name' => 'Test Guardian 2',
                'guardian_phone' => '+1-555-2222',
                'address' => '789 Test Blvd',
            ]);

        $response->assertStatus(201);
    }

    public function test_duplicate_email_within_school_is_rejected(): void
    {
        // Create first student
        $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/students', [
                'name' => 'Test Student',
                'email' => 'duplicate@test.edu',
                'password' => 'password123',
                'student_id' => 'S001',
                'date_of_birth' => '2015-01-01',
                'guardian_name' => 'Test Guardian',
                'guardian_phone' => '+1-555-1111',
                'address' => '456 Test Ave',
            ]);

        // Try to create second student with same email
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/students', [
                'name' => 'Test Student 2',
                'email' => 'duplicate@test.edu',
                'password' => 'password123',
                'student_id' => 'S002',
                'date_of_birth' => '2015-02-01',
                'guardian_name' => 'Test Guardian 2',
                'guardian_phone' => '+1-555-2222',
                'address' => '789 Test Blvd',
            ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email']);
    }

    public function test_admin_can_list_students_with_pagination(): void
    {
        // Create multiple students
        for ($i = 1; $i <= 20; $i++) {
            $user = User::create([
                'school_id' => $this->school->id,
                'name' => "Student {$i}",
                'email' => "student{$i}@test.edu",
                'password' => Hash::make('password'),
                'role' => 'student',
            ]);

            Student::create([
                'school_id' => $this->school->id,
                'class_id' => $this->class->id,
                'user_id' => $user->id,
                'student_id' => "S{$i}",
                'date_of_birth' => '2015-01-01',
                'guardian_name' => "Guardian {$i}",
                'guardian_phone' => "+1-555-{$i}",
                'address' => "{$i} Test St",
            ]);
        }

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/students');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'student_id',
                            'user' => ['id', 'name', 'email'],
                            'class' => ['id', 'name', 'grade_level'],
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total',
                    ]
                ]);

        $this->assertEquals(15, count($response->json('data')));
        $this->assertEquals(20, $response->json('meta.total'));
    }

    public function test_student_cannot_access_other_students(): void
    {
        // Create student user
        $studentUser = User::create([
            'school_id' => $this->school->id,
            'name' => 'Student User',
            'email' => 'student@test.edu',
            'password' => Hash::make('password'),
            'role' => 'student',
        ]);

        $response = $this->actingAs($studentUser, 'sanctum')
            ->getJson('/api/students');

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_access_students(): void
    {
        $response = $this->getJson('/api/students');

        $response->assertStatus(401);
    }
}