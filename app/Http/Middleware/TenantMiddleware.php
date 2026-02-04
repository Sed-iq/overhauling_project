<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Set the tenant context for the current request
            app()->instance('current_school_id', $user->school_id);
            
            // Add global scope to ensure all queries are scoped to the current school
            $this->addGlobalScope();
        }

        return $next($request);
    }

    /**
     * Add global scope to models that should be tenant-aware
     */
    private function addGlobalScope(): void
    {
        $schoolId = app('current_school_id');
        
        if ($schoolId) {
            // Add global scopes to tenant-aware models
            \App\Models\Student::addGlobalScope('school', function ($builder) use ($schoolId) {
                $builder->where('school_id', $schoolId);
            });
            
            \App\Models\Teacher::addGlobalScope('school', function ($builder) use ($schoolId) {
                $builder->where('school_id', $schoolId);
            });
            
            \App\Models\ClassModel::addGlobalScope('school', function ($builder) use ($schoolId) {
                $builder->where('school_id', $schoolId);
            });
        }
    }
}