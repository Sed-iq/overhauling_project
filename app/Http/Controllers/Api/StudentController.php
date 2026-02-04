<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Student::with(['user:id,name,email', 'class:id,name,grade_level'])
                        ->where('school_id', $request->user()->school_id);

        // Apply filters if provided
        if ($request->has('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('student_id', 'like', "%{$search}%");
        }

        $students = $query->paginate(15);

        return response()->json([
            'data' => $students->items(),
            'meta' => [
                'current_page' => $students->currentPage(),
                'last_page' => $students->lastPage(),
                'per_page' => $students->perPage(),
                'total' => $students->total(),
            ]
        ]);
    }

    /**
     * Store a newly created student.
     */
    public function store(StoreStudentRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Create user account
            $user = User::create([
                'school_id' => $request->user()->school_id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
            ]);

            // Create student record
            $student = Student::create([
                'school_id' => $request->user()->school_id,
                'class_id' => $request->class_id,
                'user_id' => $user->id,
                'student_id' => $request->student_id,
                'date_of_birth' => $request->date_of_birth,
                'guardian_name' => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'address' => $request->address,
            ]);

            $student->load(['user:id,name,email', 'class:id,name,grade_level']);

            DB::commit();

            return response()->json([
                'message' => 'Student created successfully',
                'data' => $student
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to create student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student): JsonResponse
    {
        $student->load(['user:id,name,email', 'class:id,name,grade_level']);
        
        return response()->json([
            'data' => $student
        ]);
    }

    /**
     * Update the specified student.
     */
    public function update(UpdateStudentRequest $request, Student $student): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Update user if user-related fields are provided
            $userFields = array_filter([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? Hash::make($request->password) : null,
            ]);

            if (!empty($userFields)) {
                $student->user->update($userFields);
            }

            // Update student fields
            $studentFields = array_filter([
                'class_id' => $request->class_id,
                'student_id' => $request->student_id,
                'date_of_birth' => $request->date_of_birth,
                'guardian_name' => $request->guardian_name,
                'guardian_phone' => $request->guardian_phone,
                'address' => $request->address,
            ]);

            if (!empty($studentFields)) {
                $student->update($studentFields);
            }

            $student->load(['user:id,name,email', 'class:id,name,grade_level']);

            DB::commit();

            return response()->json([
                'message' => 'Student updated successfully',
                'data' => $student
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to update student',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified student.
     */
    public function destroy(Student $student): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Soft delete the student
            $student->delete();

            // Optionally, you might want to deactivate the user account
            // $student->user->update(['email_verified_at' => null]);

            DB::commit();

            return response()->json([
                'message' => 'Student deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}