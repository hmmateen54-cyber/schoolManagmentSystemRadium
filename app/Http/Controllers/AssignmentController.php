<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacherProfile;

        $assignments = Assignment::with('schoolClass')
            ->where('teacher_id', $teacher->id)
            ->latest('assigned_date')
            ->latest()
            ->get();

        $classes = SchoolClass::where('teacher_id', $teacher->id)
            ->get();

        return inertia('Assignments/Index', [
            'assignments' => $assignments,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request)
    {
        $teacher = auth()->user()->teacherProfile;

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:classwork,homework',
            'assigned_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:assigned_date',
        ]);

        $class = SchoolClass::where('id', $validated['class_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        Assignment::create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject' => $validated['subject'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'assigned_date' => $validated['assigned_date'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('teacher.assignments.index');
    }

    public function update(Request $request, Assignment $assignment)
    {
        $teacher = auth()->user()->teacherProfile;

        abort_unless(
            $assignment->teacher_id === $teacher->id,
            403
        );

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:classwork,homework',
            'assigned_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:assigned_date',
        ]);

        $class = SchoolClass::where('id', $validated['class_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $assignment->update([
            'class_id' => $class->id,
            'subject' => $validated['subject'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'assigned_date' => $validated['assigned_date'],
            'due_date' => $validated['due_date'] ?? null,
        ]);

        return redirect()->route('teacher.assignments.index');
    }

    public function destroy(Assignment $assignment)
    {
        $teacher = auth()->user()->teacherProfile;

        abort_unless(
            $assignment->teacher_id === $teacher->id,
            403
        );

        $assignment->delete();

        return redirect()->route('teacher.assignments.index');
    }
}