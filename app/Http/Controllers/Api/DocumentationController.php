<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class DocumentationController extends Controller
{
    /**
     * Get API documentation.
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'name' => 'School Management API',
            'version' => '1.0.0',
            'description' => 'REST API for School Management SaaS platform',
            'endpoints' => [
                'authentication' => [
                    'POST /api/login' => 'User login',
                    'POST /api/logout' => 'User logout (requires auth)',
                    'GET /api/profile' => 'Get user profile (requires auth)',
                ],
                'students' => [
                    'GET /api/students' => 'List students with pagination (admin/teacher)',
                    'POST /api/students' => 'Create new student (admin/teacher)',
                    'GET /api/students/{id}' => 'Get student details (admin/teacher)',
                    'PUT /api/students/{id}' => 'Update student (admin/teacher)',
                    'DELETE /api/students/{id}' => 'Delete student (admin only)',
                    'GET /api/students/me' => 'Get own profile (student only)',
                ],
                'teachers' => [
                    'POST /api/teachers/{id}/assign-class' => 'Assign teacher to class (admin only)',
                    'GET /api/teachers/{id}/classes' => 'Get teacher assigned classes (admin/teacher)',
                ],
            ],
            'authentication' => [
                'type' => 'Bearer Token',
                'header' => 'Authorization: Bearer {token}',
                'note' => 'Obtain token via POST /api/login',
            ],
            'roles' => [
                'school_admin' => 'Full access to school data',
                'teacher' => 'Access to assigned classes and students',
                'student' => 'Access to own profile only',
            ],
            'tenant_isolation' => 'All data is automatically scoped by school_id',
            'sample_credentials' => [
                'school_admin' => [
                    'email' => 'admin@greenwood.edu',
                    'password' => 'password123',
                ],
                'teacher' => [
                    'email' => 'mary@greenwood.edu',
                    'password' => 'password123',
                ],
                'student' => [
                    'email' => 'alice@student.greenwood.edu',
                    'password' => 'password123',
                ],
            ],
        ]);
    }
}