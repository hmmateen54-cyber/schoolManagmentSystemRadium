<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SchoolClassController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Classes/Index', [
            'classes' => SchoolClass::query()
                ->with('teacher')
                ->withCount('students')
                ->orderBy('name')
                ->orderBy('section')
                ->paginate(10),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Classes/Create', [
            'teachers' => $this->availableTeachers(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules($request));
        $validated['section'] = $validated['section'] ?? '';

        SchoolClass::create($validated);

        return redirect()->route('classes.index');
    }

    public function show(SchoolClass $schoolClass): Response
    {
        return Inertia::render('Classes/Show', [
            'schoolClass' => $schoolClass->load([
                'teacher',
                'students' => fn ($query) => $query->orderBy('name'),
            ]),
        ]);
    }

    public function edit(SchoolClass $schoolClass): Response
    {
        return Inertia::render('Classes/Edit', [
            'schoolClass' => $schoolClass,
            'teachers' => $this->availableTeachers($schoolClass),
        ]);
    }

    public function update(Request $request, SchoolClass $schoolClass)
    {
        $validated = $request->validate($this->rules($request, $schoolClass));
        $validated['section'] = $validated['section'] ?? '';

        DB::transaction(function () use ($schoolClass, $validated) {
            $schoolClass->update($validated);
            $schoolClass->students()->update(['class' => $schoolClass->label]);
        });

        return redirect()->route('classes.index');
    }

    public function destroy(SchoolClass $schoolClass)
    {
        if ($schoolClass->students()->exists()) {
            return back()->withErrors([
                'class' => 'Move the students to another class before deleting this class.',
            ]);
        }

        $schoolClass->delete();

        return redirect()->route('classes.index');
    }

    private function rules(Request $request, ?SchoolClass $schoolClass = null): array
    {
        $sectionInput = $request->input('section');
        $section = is_string($sectionInput) ? $sectionInput : '';

        return [
            'name' => [
                'bail',
                'required',
                'string',
                'max:255',
                Rule::unique('classes', 'name')
                    ->where(fn ($query) => $query->where('section', $section))
                    ->ignore($schoolClass?->id),
            ],
            'section' => ['bail', 'nullable', 'string', 'max:50'],
            'teacher_id' => [
                'bail',
                'nullable',
                'integer',
                'exists:teachers,id',
                Rule::unique('classes', 'teacher_id')->ignore($schoolClass?->id),
            ],
        ];
    }

    private function availableTeachers(?SchoolClass $schoolClass = null)
    {
        return Teacher::query()
            ->where(function ($query) use ($schoolClass) {
                $query->whereDoesntHave('schoolClass');

                if ($schoolClass?->teacher_id) {
                    $query->orWhere('id', $schoolClass->teacher_id);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'subject', 'user_id']);
    }
}
