<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentPortalController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\TeacherPortalController;
use App\Http\Controllers\TeacherScheduleController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AssignmentController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', DashboardController::class)
    ->middleware('auth')
    ->name('dashboard');

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::resource('students', StudentController::class);

    Route::middleware('role:principal')->group(function () {
        Route::resource('teachers', TeacherController::class)
            ->except(['show']);

        Route::resource('classes', SchoolClassController::class)
            ->parameters(['classes' => 'schoolClass']);
    });

    Route::middleware('role:student')->group(function () {
        Route::get('/my-profile', [StudentPortalController::class, 'show'])
            ->name('student.profile');
    });

    Route::middleware('role:teacher')->group(function () {

        Route::get('/my-class', [TeacherPortalController::class, 'show'])
            ->name('teacher.class');

        Route::get('/teacher/schedules', [TeacherScheduleController::class, 'index'])
            ->name('teacher.schedules.index');

        Route::post('/teacher/schedules', [TeacherScheduleController::class, 'store'])
            ->name('teacher.schedules.store');

        Route::delete('/teacher/schedules/{teacherSchedule}', [TeacherScheduleController::class, 'destroy'])
            ->name('teacher.schedules.destroy');

        Route::get('/teacher/assignments', [AssignmentController::class, 'index'])
            ->name('teacher.assignments.index');

        Route::post('/teacher/assignments', [AssignmentController::class, 'store'])
            ->name('teacher.assignments.store');

        Route::put('/teacher/schedules/{teacherSchedule}', [TeacherScheduleController::class, 'update'])
            ->name('teacher.schedules.update');

        Route::put('/teacher/assignments/{assignment}', [AssignmentController::class, 'update'])
            ->name('teacher.assignments.update');
            });

        Route::delete('/teacher/assignments/{assignment}', [AssignmentController::class, 'destroy'])
            ->name('teacher.assignments.destroy');    
});

require __DIR__.'/auth.php';