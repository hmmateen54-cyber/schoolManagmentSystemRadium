<?php

namespace Tests\Feature;

use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RoleBasedSchoolAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_principal_can_manage_students_teachers_and_classes(): void
    {
        $principal = $this->userWithRole(User::ROLE_PRINCIPAL);
        $teacherAccount = User::factory()->create();
        $this->actingAs($principal)
            ->post(route('teachers.store'), [
                'name' => 'Teacher One',
                'email' => 'teacher-one@example.test',
                'phone' => null,
                'subject' => 'Math',
                'user_id' => $teacherAccount->id,
            ])
            ->assertRedirect(route('teachers.index'));

        $teacher = Teacher::where('email', 'teacher-one@example.test')->firstOrFail();
        $teacherAccount->refresh();

        $this->assertSame(User::ROLE_TEACHER, $teacherAccount->role);

        $this->actingAs($principal)
            ->post(route('classes.store'), [
                'name' => 'Class 5',
                'section' => 'A',
                'teacher_id' => $teacher->id,
            ])
            ->assertRedirect(route('classes.index'));

        $schoolClass = SchoolClass::firstOrFail();

        $studentAccount = User::factory()->create();
        $this->actingAs($principal)
            ->post(route('students.store'), [
                'name' => 'Amina Student',
                'email' => 'amina@example.test',
                'phone' => '123',
                'age' => 12,
                'class_id' => $schoolClass->id,
                'user_id' => $studentAccount->id,
            ])
            ->assertRedirect(route('students.index'));

        $student = Student::where('email', 'amina@example.test')->firstOrFail();
        $this->assertSame($studentAccount->id, $student->user_id);
        $this->assertSame($schoolClass->id, $student->class_id);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('role', User::ROLE_PRINCIPAL)
                ->where('metrics.students', 1)
                ->where('metrics.teachers', 1)
                ->where('metrics.classes', 1));

        for ($index = 1; $index <= 10; $index++) {
            $this->makeStudent("Extra Student {$index}", "extra-student-{$index}@example.test", $schoolClass);
            Teacher::create([
                'name' => "Extra Teacher {$index}",
                'email' => "extra-teacher-{$index}@example.test",
                'phone' => null,
                'subject' => 'Science',
            ]);
        }

        $this->get(route('students.index', ['search' => 'Amina']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Students/Index')
                ->has('students.data', 1)
                ->where('students.data.0.id', $student->id));

        $this->get(route('students.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('students.per_page', 10)
                ->where('students.total', 11));

        $this->put(route('students.update', $student), [
            'name' => 'Amina Updated',
            'email' => $student->email,
            'phone' => '456',
            'age' => 13,
            'class_id' => $schoolClass->id,
            'user_id' => $studentAccount->id,
        ])->assertRedirect(route('students.index'));

        $student->refresh();
        $this->assertSame('Amina Updated', $student->name);
        $this->assertSame('Class 5-A', $student->class);

        $this->get(route('teachers.index', ['search' => 'Teacher One']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teachers/Index')
                ->has('teachers.data', 1)
                ->where('teachers.data.0.id', $teacher->id));

        $this->get(route('teachers.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('teachers.per_page', 10)
                ->where('teachers.total', 11));

        $this->put(route('teachers.update', $teacher), [
            'name' => 'Teacher Updated',
            'email' => $teacher->email,
            'phone' => null,
            'subject' => 'Physics',
            'user_id' => $teacherAccount->id,
        ])->assertRedirect(route('teachers.index'));

        $teacher->refresh();
        $this->assertSame('Physics', $teacher->subject);

        $this->get(route('classes.show', $schoolClass))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Classes/Show')
                ->where('schoolClass.id', $schoolClass->id));

        $this->put(route('classes.update', $schoolClass), [
            'name' => 'Class 5',
            'section' => 'B',
            'teacher_id' => $teacher->id,
        ])->assertRedirect(route('classes.index'));

        $this->assertSame('Class 5-B', $student->fresh()->class);

        $this->delete(route('classes.destroy', $schoolClass))
            ->assertSessionHasErrors('class');

        $otherClass = SchoolClass::create(['name' => 'Class 6', 'section' => 'A']);
        $this->put(route('students.update', $student), [
            'name' => $student->name,
            'email' => $student->email,
            'phone' => $student->phone,
            'age' => $student->age,
            'class_id' => $otherClass->id,
            'user_id' => $studentAccount->id,
        ])->assertRedirect(route('students.index'));

        Student::where('class_id', $schoolClass->id)->update([
            'class_id' => $otherClass->id,
            'class' => $otherClass->label,
        ]);

        $this->delete(route('classes.destroy', $schoolClass))
            ->assertRedirect(route('classes.index'));

        $this->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'));
        $this->assertDatabaseMissing('students', ['id' => $student->id]);

        $this->delete(route('teachers.destroy', $teacher))
            ->assertRedirect(route('teachers.index'));
        $this->assertDatabaseMissing('teachers', ['id' => $teacher->id]);
    }

    public function test_teacher_is_confined_to_the_assigned_class_and_cannot_manage_school_data(): void
    {
        $teacherUser = $this->userWithRole(User::ROLE_TEACHER);
        $otherTeacherUser = $this->userWithRole(User::ROLE_TEACHER);
        $teacher = Teacher::create([
            'name' => 'Ali Teacher',
            'email' => 'ali-teacher@example.test',
            'subject' => 'Math',
            'user_id' => $teacherUser->id,
        ]);
        $otherTeacher = Teacher::create([
            'name' => 'Ahmed Teacher',
            'email' => 'ahmed-teacher@example.test',
            'subject' => 'Science',
            'user_id' => $otherTeacherUser->id,
        ]);
        $aliClass = SchoolClass::create(['name' => 'Class 5', 'section' => 'A', 'teacher_id' => $teacher->id]);
        $ahmedClass = SchoolClass::create(['name' => 'Class 6', 'section' => 'A', 'teacher_id' => $otherTeacher->id]);
        $aliStudent = $this->makeStudent('Ali Class Student', 'ali-student@example.test', $aliClass);
        $ahmedStudent = $this->makeStudent('Ahmed Class Student', 'ahmed-student@example.test', $ahmedClass);

        $this->actingAs($teacherUser)
            ->get(route('students.index', ['search' => 'Student']))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->has('students.data', 1)
                ->where('students.data.0.id', $aliStudent->id));

        $this->get(route('teacher.class'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Teachers/MyClass')
                ->where('schoolClass.id', $aliClass->id));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('role', User::ROLE_TEACHER)
                ->where('assignedClass.id', $aliClass->id)
                ->where('studentCount', 1));

        $this->get(route('students.edit', $aliStudent))->assertOk();
        $this->put(route('students.update', $aliStudent), [
            'name' => 'Ali Updated',
            'email' => $aliStudent->email,
            'phone' => null,
            'age' => 14,
            'class_id' => $ahmedClass->id,
        ])->assertRedirect(route('students.index'));

        $this->assertSame($aliClass->id, $aliStudent->fresh()->class_id);

        $this->get(route('students.show', $ahmedStudent))->assertForbidden();
        $this->get(route('students.edit', $ahmedStudent))->assertForbidden();
        $this->put(route('students.update', $ahmedStudent), [
            'name' => 'Unauthorized Change',
            'email' => $ahmedStudent->email,
            'age' => 14,
        ])->assertForbidden();
        $this->delete(route('students.destroy', $ahmedStudent))->assertForbidden();
        $this->get(route('students.create'))->assertForbidden();
        $this->delete(route('students.destroy', $aliStudent))->assertForbidden();

        $this->get(route('teachers.index'))->assertForbidden();
        $this->get(route('teachers.create'))->assertForbidden();
        $this->get(route('teachers.edit', $otherTeacher))->assertForbidden();
        $this->put(route('teachers.update', $otherTeacher), [
            'name' => 'Unauthorized Teacher Change',
            'email' => $otherTeacher->email,
            'subject' => $otherTeacher->subject,
        ])->assertForbidden();
        $this->delete(route('teachers.destroy', $otherTeacher))->assertForbidden();
        $this->get(route('classes.index'))->assertForbidden();
        $this->get(route('classes.create'))->assertForbidden();
        $this->get(route('classes.show', $aliClass))->assertForbidden();
        $this->get(route('classes.edit', $aliClass))->assertForbidden();
        $this->put(route('classes.update', $aliClass), [
            'name' => $aliClass->name,
            'section' => $aliClass->section,
        ])->assertForbidden();
        $this->delete(route('classes.destroy', $aliClass))->assertForbidden();
    }

    public function test_student_can_only_view_their_own_profile_and_cannot_use_management_routes(): void
    {
        $studentUser = $this->userWithRole(User::ROLE_STUDENT);
        $otherStudentUser = $this->userWithRole(User::ROLE_STUDENT);
        $schoolClass = SchoolClass::create(['name' => 'Class 8', 'section' => 'A']);
        $ownStudent = $this->makeStudent('My Record', 'my-record@example.test', $schoolClass, $studentUser);
        $otherStudent = $this->makeStudent('Other Record', 'other-record@example.test', $schoolClass, $otherStudentUser);

        $this->actingAs($studentUser)
            ->get(route('student.profile'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Students/MyProfile')
                ->where('student.id', $ownStudent->id));

        $this->get(route('students.show', $ownStudent))->assertOk();
        $this->get(route('students.show', $otherStudent))->assertForbidden();
        $this->get(route('students.index'))->assertForbidden();
        $this->get(route('students.edit', $ownStudent))->assertForbidden();
        $this->get(route('students.edit', $otherStudent))->assertForbidden();
        $this->put(route('students.update', $ownStudent), [
            'name' => 'Changed',
            'email' => $ownStudent->email,
            'age' => 15,
        ])->assertForbidden();
        $this->delete(route('students.destroy', $ownStudent))->assertForbidden();

        $this->get(route('teachers.index'))->assertForbidden();
        $this->get(route('teachers.create'))->assertForbidden();
        $this->get(route('teachers.edit', Teacher::create([
            'name' => 'Unlinked Teacher',
            'email' => 'unlinked-teacher@example.test',
            'subject' => 'Math',
        ])))->assertForbidden();
        $this->get(route('classes.index'))->assertForbidden();
        $this->get(route('classes.create'))->assertForbidden();
        $this->get(route('classes.show', $schoolClass))->assertForbidden();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->where('role', User::ROLE_STUDENT)
                ->where('student.id', $ownStudent->id)
                ->missing('metrics'));
    }

    public function test_logged_out_users_cannot_access_role_protected_pages(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('students.index'))->assertRedirect(route('login'));
        $this->get(route('teachers.index'))->assertRedirect(route('login'));
        $this->get(route('classes.index'))->assertRedirect(route('login'));
        $this->get(route('student.profile'))->assertRedirect(route('login'));
        $this->get(route('teacher.class'))->assertRedirect(route('login'));
    }

    public function test_public_registration_cannot_assign_a_privileged_role(): void
    {
        $this->post(route('register'), [
            'name' => 'New Account',
            'email' => 'new-account@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'role' => User::ROLE_PRINCIPAL,
        ])->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('users', [
            'email' => 'new-account@example.test',
            'role' => User::ROLE_STUDENT,
        ]);
    }

    public function test_cli_can_promote_an_unlinked_account_to_principal(): void
    {
        $user = User::factory()->create();

        $exitCode = Artisan::call('school:make-principal', ['email' => $user->email]);

        $this->assertSame(0, $exitCode);
        $this->assertSame(User::ROLE_PRINCIPAL, $user->fresh()->role);
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => $role])->save();

        return $user;
    }

    private function makeStudent(string $name, string $email, SchoolClass $schoolClass, ?User $user = null): Student
    {
        return Student::create([
            'name' => $name,
            'email' => $email,
            'phone' => null,
            'class' => $schoolClass->label,
            'age' => 12,
            'class_id' => $schoolClass->id,
            'user_id' => $user?->id,
        ]);
    }
}
