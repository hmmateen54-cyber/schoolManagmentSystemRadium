<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class TeacherController extends Controller
{
    public function index(Request $request): Response
    {
        $this->authorize('viewAny', Teacher::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
        ]);

        $teachers = Teacher::query()
            ->with(['schoolClass', 'user'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhereHas('schoolClass', function ($classQuery) use ($search) {
                            $classQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('section', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Teachers/Index', [
            'teachers' => $teachers,
            'filters' => [
                'search' => $filters['search'] ?? null,
            ],
        ]);
    }

    public function create(): Response
    {
        $this->authorize('create', Teacher::class);

        return Inertia::render('Teachers/Create', [
            'accountOptions' => $this->availableTeacherAccounts(),
        ]);
    }

    public function store(Request $request)
    {
        $this->authorize('create', Teacher::class);

        $validated = $request->validate([
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'email', 'max:255', Rule::unique(Teacher::class, 'email')],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:100'],
            'user_id' => ['bail', 'nullable', 'integer', Rule::in($this->availableTeacherAccounts()->modelKeys())],
        ]);

        DB::transaction(function () use ($validated) {
            Teacher::create($validated);

            if (! empty($validated['user_id'])) {
                User::whereKey($validated['user_id'])->update(['role' => User::ROLE_TEACHER]);
            }
        });

        return redirect()->route('teachers.index');
    }

    public function edit(Teacher $teacher): Response
    {
        $this->authorize('update', $teacher);

        return Inertia::render('Teachers/Edit', [
            'teacher' => $teacher,
            'accountOptions' => $this->availableTeacherAccounts($teacher),
        ]);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $this->authorize('update', $teacher);

        $validated = $request->validate([
            'name' => ['bail', 'required', 'string', 'max:255'],
            'email' => ['bail', 'required', 'email', 'max:255', Rule::unique(Teacher::class, 'email')->ignore($teacher)],
            'phone' => ['nullable', 'string', 'max:20'],
            'subject' => ['required', 'string', 'max:100'],
            'user_id' => ['bail', 'nullable', 'integer', Rule::in($this->availableTeacherAccounts($teacher)->modelKeys())],
        ]);

        DB::transaction(function () use ($teacher, $validated) {
            $teacher->update($validated);

            if (! empty($validated['user_id'])) {
                User::whereKey($validated['user_id'])->update(['role' => User::ROLE_TEACHER]);
            }
        });

        return redirect()->route('teachers.index');
    }

    public function destroy(Teacher $teacher)
    {
        $this->authorize('delete', $teacher);

        $teacher->delete();

        return redirect()->route('teachers.index');
    }

    private function availableTeacherAccounts(?Teacher $teacher = null)
    {
        return User::query()
            ->whereIn('role', [User::ROLE_STUDENT, User::ROLE_TEACHER])
            ->whereDoesntHave('studentProfile')
            ->where(function ($query) use ($teacher) {
                $query->whereDoesntHave('teacherProfile');

                if ($teacher?->user_id) {
                    $query->orWhere('id', $teacher->user_id);
                }
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'role']);
    }
}
