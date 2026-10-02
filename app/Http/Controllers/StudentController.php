<?php

namespace App\Http\Controllers;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Student::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $query = Student::query()->with('schoolClass');
        $user = $request->user();

        if ($user->isTeacher()) {
            $classId = $user->teacherProfile()->with('schoolClass')->first()?->schoolClass?->id;
            abort_if($classId === null, 403);
            $query->where('class_id', $classId);
        }

        $query->when($filters['search'] ?? null, function ($query, $search) {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('class', 'like', "%{$search}%")
                    ->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                        $classQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('section', 'like', "%{$search}%");
                    });
            });
        });

        return Inertia::render('Students/Index', [
            'students' => $query->latest()->paginate(10)->withQueryString(),
            'filters' => [
                'search' => $filters['search'] ?? null,
            ],
            'canEdit' => $user->isPrincipal() || $user->isTeacher(),
            'canDelete' => $user->isPrincipal(),
            'canCreate' => $user->isPrincipal(),
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Student::class);

        return Inertia::render('Students/Create', [
            'schoolClasses' => SchoolClass::query()->orderBy('name')->orderBy('section')->get(),
            'accountOptions' => $this->availableStudentAccounts(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Student::class);

        $validated = $request->validate([
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'email', 'max:255', Rule::unique(Student::class, 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'age' => ['required', 'integer', 'min:3', 'max:30'],
            'class_id' => ['bail', 'required', 'integer', 'exists:classes,id'],
            'user_id' => ['bail', 'nullable', 'integer', Rule::in($this->availableStudentAccounts()->modelKeys())],
        ]);

        $validated['class'] = SchoolClass::findOrFail($validated['class_id'])->label;

        Student::create($validated);

        return redirect()->route('students.index');
    }

    public function show(Student $student): Response
    {
        $this->authorize('view', $student);

        return Inertia::render('Students/Show', [
            'student' => $student->load(['schoolClass.teacher']),
        ]);
    }

    public function edit(Request $request, Student $student): Response
    {
        $this->authorize('update', $student);

        $props = ['student' => $student->load('schoolClass')];

        if ($request->user()->isPrincipal()) {
            $props['schoolClasses'] = SchoolClass::query()->orderBy('name')->orderBy('section')->get();
            $props['accountOptions'] = $this->availableStudentAccounts($student);
        }

        return Inertia::render('Students/Edit', $props);
    }

    public function update(Request $request, Student $student)
    {
        $this->authorize('update', $student);

        $principal = $request->user()->isPrincipal();
        $rules = [
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'email', 'max:255', Rule::unique(Student::class, 'email')->ignore($student)],
            'phone' => ['nullable', 'string', 'max:20'],
            'age' => ['required', 'integer', 'min:3', 'max:30'],
        ];

        if ($principal) {
            $rules['class_id'] = ['bail', 'required', 'integer', 'exists:classes,id'];
            $rules['user_id'] = ['bail', 'nullable', 'integer', Rule::in($this->availableStudentAccounts($student)->modelKeys())];
        }

        $validated = $request->validate($rules);

        if ($principal) {
            $validated['class'] = SchoolClass::findOrFail($validated['class_id'])->label;
        }

        $student->update($validated);

        return redirect()->route('students.index');
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', $student);

        $student->delete();

        return redirect()->route('students.index');
    }

    private function availableStudentAccounts(?Student $student = null)
    {
        return User::query()
            ->where('role', User::ROLE_STUDENT)
            ->where(function ($query) use ($student) {
                $query->where(function ($query) {
                    $query->whereDoesntHave('studentProfile')
                        ->whereDoesntHave('teacherProfile');
                });

                if ($student?->user_id) {
                    $query->orWhere('id', $student->user_id);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);
    }
}
