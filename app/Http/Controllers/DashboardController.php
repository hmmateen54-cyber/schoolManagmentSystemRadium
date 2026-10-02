<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        abort_unless(in_array($user->role, User::ROLES, true), 403);

        $props = [
            'role' => $user->role,
        ];

        if ($user->isPrincipal()) {
            $props['metrics'] = [
                'students' => Student::count(),
                'teachers' => Teacher::count(),
                'classes' => SchoolClass::count(),
            ];
        } elseif ($user->isTeacher()) {
            $teacher = $user->teacherProfile()
                ->with('schoolClass')
                ->first();

            $schoolClass = $teacher?->schoolClass;

            $today = now()->format('l');

            $props['assignedClass'] = $schoolClass?->loadCount('students');
            $props['studentCount'] = $schoolClass?->students_count ?? 0;

            $props['todaySchedules'] = $teacher
                ? $teacher->schedules()
                    ->with('schoolClass')
                    ->where('day', $today)
                    ->orderBy('start_time')
                    ->get()
                : [];

            $props['recentAssignments'] = $teacher
                ? $teacher->assignments()
                    ->with('schoolClass')
                    ->latest('assigned_date')
                    ->latest()
                    ->take(5)
                    ->get()
                : [];
        } else {
            $student = $user->studentProfile()
                ->with('schoolClass.teacher')
                ->first();

            $props['student'] = $student;
            $props['classTeacher'] = $student?->schoolClass?->teacher?->name;
        }

        return Inertia::render('Dashboard', $props);
    }
}