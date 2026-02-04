<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Student;
use App\Models\Teacher;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Teacher::class, TeacherPolicy::class);
    }
}