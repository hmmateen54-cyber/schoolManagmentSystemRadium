<?php

namespace App\Http\Controllers;

use App\Models\TeacherSchedule;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class TeacherScheduleController extends Controller
{
    public function index()
    {
        $teacher = auth()->user()->teacherProfile;

        $schedules = TeacherSchedule::with('schoolClass')
            ->where('teacher_id', $teacher->id)
            ->orderBy('day')
            ->orderBy('start_time')
            ->get();

        $classes = SchoolClass::where('teacher_id', $teacher->id)->get();

        return inertia('TeacherSchedules/Index', [
            'schedules' => $schedules,
            'classes' => $classes,
        ]);
    }

    public function store(Request $request)
    {
        $teacher = auth()->user()->teacherProfile;

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'day' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $class = SchoolClass::where('id', $validated['class_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        TeacherSchedule::create([
            'teacher_id' => $teacher->id,
            'class_id' => $class->id,
            'subject' => $validated['subject'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return redirect()->route('teacher.schedules.index');
    }

    public function update(Request $request, TeacherSchedule $teacherSchedule)
    {
        $teacher = auth()->user()->teacherProfile;

        abort_unless(
            $teacherSchedule->teacher_id === $teacher->id,
            403
        );

        $validated = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject' => 'required|string|max:255',
            'day' => 'required|string|max:20',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $class = SchoolClass::where('id', $validated['class_id'])
            ->where('teacher_id', $teacher->id)
            ->firstOrFail();

        $teacherSchedule->update([
            'class_id' => $class->id,
            'subject' => $validated['subject'],
            'day' => $validated['day'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        return redirect()->route('teacher.schedules.index');
    }

    public function destroy(TeacherSchedule $teacherSchedule)
    {
        $teacher = auth()->user()->teacherProfile;

        abort_unless(
            $teacherSchedule->teacher_id === $teacher->id,
            403
        );

        $teacherSchedule->delete();

        return redirect()->route('teacher.schedules.index');
    }
}