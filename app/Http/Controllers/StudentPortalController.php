<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class StudentPortalController extends Controller
{
    public function show(Request $request): Response
    {
        $student = $request->user()
            ->studentProfile()
            ->with([
                'schoolClass.teacher',
                'schoolClass.assignments' => function ($query) {
                    $query->latest('assigned_date')
                        ->latest();
                },
            ])
            ->first();

        if ($student) {
            $this->authorize('view', $student);
        }

        return Inertia::render('Students/MyProfile', [
            'student' => $student,
            'assignments' => $student?->schoolClass?->assignments ?? [],
        ]);
    }
}