<?php

namespace App\Providers;

use App\Models\Student;
use App\Models\Teacher;
use App\Policies\StudentPolicy;
use App\Policies\TeacherPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(Student::class, StudentPolicy::class);
        Gate::policy(Teacher::class, TeacherPolicy::class);

        Vite::prefetch(concurrency: 3);
    }
}
