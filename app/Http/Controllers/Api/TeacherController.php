<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AssignTeacherRequest;
use App\Models\Teacher;
use App\Models\TeacherClassAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TeacherController extends Controller
{
    /**
     * Assign a teacher to a class.
     */
    public function assignClass(AssignTeacherRequest $request, Teacher $teacher): JsonResponse
    {
        try {
            // Verify teacher belongs to the same school
            if ($teacher->school_id !== $request->user()->school_id) {
                return response()->json([
                    'message' => 'Teacher not found in your school'
                ], 404);
            }

            $assignment = TeacherClassAssignment::create([
                'teacher_id' => $teacher->id,
                'class_id' => $request->class_id,
                'subject' => $request->subject,
            ]);

            $assignment->load(['teacher.user:id,name,email', 'class:id,name,grade_level']);

            return response()->json([
                'message' => 'Teacher assigned to class successfully',
                'data' => $assignment
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to assign teacher to class',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get teacher's assigned classes.
     */
    public function getAssignedClasses(Request $request, Teacher $teacher): JsonResponse
    {
        // Verify teacher belongs to the same school or is the authenticated teacher
        $user = $request->user();
        if ($teacher->school_id !== $user->school_id || 
            ($user->isTeacher() && $user->teacher->id !== $teacher->id)) {
            return response()->json([
                'message' => 'Access denied'
            ], 403);
        }

        $assignments = TeacherClassAssignment::with(['class:id,name,grade_level'])
                                           ->where('teacher_id', $teacher->id)
                                           ->get();

        return response()->json([
            'data' => $assignments
        ]);
    }
}