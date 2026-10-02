<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;

class StudentPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->isPrincipal()) {
            return true;
        }

        return $user->isTeacher() && $this->assignedClassId($user) !== null;
    }

    public function view(User $user, Student $student): bool
    {
        if ($user->isPrincipal()) {
            return true;
        }

        if ($user->isStudent()) {
            return $student->user_id === $user->id;
        }

        return $user->isTeacher()
            && $student->class_id !== null
            && $student->class_id === $this->assignedClassId($user);
    }

    public function create(User $user): bool
    {
        return $user->isPrincipal();
    }

    public function update(User $user, Student $student): bool
    {
        return $user->isPrincipal()
            || ($user->isTeacher() && $this->view($user, $student));
    }

    public function delete(User $user, Student $student): bool
    {
        return $user->isPrincipal();
    }

    private function assignedClassId(User $user): ?int
    {
        $teacher = $user->teacherProfile()->with('schoolClass')->first();

        return $teacher?->schoolClass?->id;
    }
}
