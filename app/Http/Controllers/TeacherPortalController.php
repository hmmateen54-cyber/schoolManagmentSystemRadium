<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TeacherPortalController extends Controller
{
    public function show(Request $request): Response
    {
        $teacher = $request->user()
            ->teacherProfile()
            ->with('schoolClass.students')
            ->first();

        return Inertia::render('Teachers/MyClass', [
            'schoolClass' => $teacher?->schoolClass,
        ]);
    }
}
