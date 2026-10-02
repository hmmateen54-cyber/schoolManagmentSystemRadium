<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatCard from '@/Components/StatCard.vue';
import Icon from '@/Components/Icon.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    role: String,
    metrics: Object,
    assignedClass: Object,
    studentCount: Number,
    student: Object,
    classTeacher: String,
    todaySchedules: {
        type: Array,
        default: () => [],
    },
    recentAssignments: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const classLabel = (schoolClass) => {
    if (!schoolClass) return 'Not assigned';
    return schoolClass.section ? `${schoolClass.name}-${schoolClass.section}` : schoolClass.name;
};

const formatDate = (date) => {
    if (!date) return '—';

    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    });
};

</script>

<template>

    <Head title="Dashboard" />
    <AuthenticatedLayout>
        <template #header>
            <h2>{{ role === 'principal' ? 'Principal dashboard' : role === 'teacher' ? 'Teacher dashboard' : 'Student dashboard' }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <section class="dashboard-welcome">
                    <div class="dashboard-welcome-copy">
                        <p class="dashboard-eyebrow">{{ role }} workspace</p>
                        <h2>{{ role === 'principal' ? 'A clearer view of your school.' : role === 'teacher' ? `Welcome
                            back,
                            ${page.props.auth.user.name.split(' ')[0]}.` : `Good to see you, ${student?.name?.split('')[0] ??
                            page.props.auth.user.name.split(' ')[0]}.` }}</h2>
                        <p v-if="role === 'principal'">Your school at a glance. Find the people and classes you need,
                            all in one
                            place.</p>
                        <p v-else-if="role === 'teacher'">Your classroom overview, student roster, and the details you
                            use every
                            day.</p>
                        <p v-else>Your personal school information, thoughtfully organized in one place.</p>
                    </div>
                    <div class="dashboard-welcome-aside">
                        <span class="dashboard-welcome-aside-icon">
                            <Icon :name="role === 'principal' ? 'chart' : role === 'teacher' ? 'book' : 'profile'" />
                        </span>
                        <span><strong>{{ role === 'principal' ? 'School overview' : role === 'teacher' ? 'Classroom space' :
                                'Student space' }}</strong><small>Everything in its place</small></span>
                    </div>
                </section>

                <template v-if="role === 'principal'">
                    <div class="dashboard-section-heading">
                        <div>
                            <h3>School overview</h3>
                            <p>A current look at your school community.</p>
                        </div>
                    </div>
                    <div class="dashboard-stats">
                        <StatCard label="Total students" :value="metrics.students" detail="Student records"
                            icon="students" />
                        <StatCard label="Total teachers" :value="metrics.teachers" detail="Teaching staff"
                            icon="teachers" tone="violet" />
                        <StatCard label="Total classes" :value="metrics.classes" detail="School classes" icon="classes"
                            tone="blue" />
                    </div>

                    <div class="dashboard-section-heading">
                        <div>
                            <h3>Quick actions</h3>
                            <p>Go straight to the work at hand.</p>
                        </div>
                    </div>
                    <div class="dashboard-actions">
                        <Link :href="route('students.index')" class="dashboard-action-card">
                            <span class="dashboard-action-icon">
                                <Icon name="students" />
                            </span>
                            <span class="dashboard-action-copy"><strong>Manage students</strong><small>Search, assign,
                                    and update student records.</small></span>
                            <Icon class="dashboard-action-arrow" name="arrow" :size="17" />
                        </Link>
                        <Link :href="route('teachers.index')" class="dashboard-action-card">
                            <span class="dashboard-action-icon">
                                <Icon name="teachers" />
                            </span>
                            <span class="dashboard-action-copy"><strong>Manage teachers</strong><small>Keep staff
                                    details and accounts organized.</small></span>
                            <Icon class="dashboard-action-arrow" name="arrow" :size="17" />
                        </Link>
                        <Link :href="route('classes.index')" class="dashboard-action-card">
                            <span class="dashboard-action-icon">
                                <Icon name="classes" />
                            </span>
                            <span class="dashboard-action-copy"><strong>Manage classes</strong><small>Assign teachers
                                    and place students.</small></span>
                            <Icon class="dashboard-action-arrow" name="arrow" :size="17" />
                        </Link>
                    </div>
                </template>

                <template v-else-if="role === 'teacher'">
                    <div class="dashboard-section-heading">
                        <div>
                            <h3>Your classroom</h3>
                            <p>A quick view of your assigned class.</p>
                        </div>
                    </div>

                    <div class="dashboard-role-grid">
                        <article class="dashboard-info-card">
                            <p class="dashboard-info-card-label">Assigned class</p>
                            <p class="dashboard-info-card-value">
                                {{ classLabel(assignedClass) }}
                            </p>
                            <p class="dashboard-info-card-caption">
                                Your class and its student roster.
                            </p>

                            <div v-if="!assignedClass" class="dashboard-unlinked">
                                Your account is not linked to a teacher record and class yet.
                                Please contact the Principal.
                            </div>
                        </article>

                        <StatCard label="Students in your class" :value="studentCount ?? 0"
                            detail="Current class roster" icon="students" />
                    </div>

                    <!-- Today's Schedule -->
                    <div class="dashboard-section-heading">
                        <div>
                            <h3>Today's schedule</h3>
                            <p>Your periods scheduled for today.</p>
                        </div>

                        <Link :href="route('teacher.schedules.index')"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            Manage schedule →
                        </Link>
                    </div>

                    <div v-if="todaySchedules.length === 0"
                        class="rounded-xl bg-white p-6 text-sm text-gray-500 shadow-sm ring-1 ring-gray-100">
                        Aaj ke liye koi period scheduled nahi hai.
                    </div>

                    <div v-else class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <article v-for="schedule in todaySchedules" :key="schedule.id"
                            class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold text-indigo-600">
                                    {{ schedule.subject }}
                                </span>

                                <span class="text-xs font-medium text-gray-500">
                                    {{ schedule.day }}
                                </span>
                            </div>

                            <h4 class="mt-3 text-lg font-semibold text-gray-900">
                                {{ schedule.school_class?.name }}

                                <span v-if="schedule.school_class?.section">
                                    - {{ schedule.school_class.section }}
                                </span>
                            </h4>

                            <p class="mt-2 text-sm text-gray-500">
                                {{ schedule.start_time }} - {{ schedule.end_time }}
                            </p>
                        </article>
                    </div>

                    <!-- Recent Work -->
                    <div class="dashboard-section-heading">
                        <div>
                            <h3>Recent classwork & homework</h3>
                            <p>Your latest work assigned to students.</p>
                        </div>

                        <Link :href="route('teacher.assignments.index')"
                            class="text-sm font-semibold text-indigo-600 hover:text-indigo-800">
                            Manage work →
                        </Link>
                    </div>

                    <div v-if="recentAssignments.length === 0"
                        class="rounded-xl bg-white p-6 text-sm text-gray-500 shadow-sm ring-1 ring-gray-100">
                        Abhi koi classwork ya homework nahi hai.
                    </div>

                    <div v-else class="space-y-3">
                        <article v-for="assignment in recentAssignments" :key="assignment.id"
                            class="flex flex-col gap-4 rounded-xl bg-white p-5 shadow-sm ring-1 ring-gray-100 md:flex-row md:items-center md:justify-between">
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold capitalize" :class="assignment.type === 'homework'
                                            ? 'bg-amber-100 text-amber-700'
                                            : 'bg-blue-100 text-blue-700'
                                        ">
                                        {{ assignment.type }}
                                    </span>

                                    <span class="text-sm text-gray-500">
                                        {{ assignment.subject }}
                                    </span>
                                </div>

                                <h4 class="mt-2 text-base font-semibold text-gray-900">
                                    {{ assignment.title }}
                                </h4>

                                <p class="mt-1 text-sm text-gray-500">
                                    Class {{ assignment.school_class?.name }}
                                    <span v-if="assignment.school_class?.section">
                                        - {{ assignment.school_class.section }}
                                    </span>
                                </p>
                            </div>

                            <div class="text-sm md:text-right">
                                <p class="text-gray-500">Assigned</p>
                                <p class="font-medium text-gray-800">
                                    {{ formatDate(assignment.assigned_date) }}
                                </p>

                                <p class="mt-2 text-gray-500">Due</p>
                                <p class="font-medium text-gray-800">
                                    {{ formatDate(assignment.due_date) }}
                                </p>
                            </div>
                        </article>
                    </div>

                    <!-- Quick Actions -->
                    <div class="dashboard-section-heading">
                        <div>
                            <h3>Quick actions</h3>
                            <p>Go straight to the work at hand.</p>
                        </div>
                    </div>

                    <div class="dashboard-actions">
                        <Link :href="route('teacher.class')" class="dashboard-action-card">
                            <span class="dashboard-action-icon">
                                <Icon name="book" />
                            </span>

                            <span class="dashboard-action-copy">
                                <strong>View my students</strong>
                                <small>
                                    See your class roster and student information.
                                </small>
                            </span>

                            <Icon class="dashboard-action-arrow" name="arrow" :size="17" />
                        </Link>

                        <Link :href="route('teacher.schedules.index')" class="dashboard-action-card">
                            <span class="dashboard-action-icon">
                                <Icon name="classes" />
                            </span>

                            <span class="dashboard-action-copy">
                                <strong>Manage my schedule</strong>
                                <small>
                                    Add and manage your teaching periods.
                                </small>
                            </span>

                            <Icon class="dashboard-action-arrow" name="arrow" :size="17" />
                        </Link>

                        <Link :href="route('teacher.assignments.index')" class="dashboard-action-card">
                            <span class="dashboard-action-icon">
                                <Icon name="profile" />
                            </span>

                            <span class="dashboard-action-copy">
                                <strong>Classwork & homework</strong>
                                <small>
                                    Create work for your assigned class.
                                </small>
                            </span>

                            <Icon class="dashboard-action-arrow" name="arrow" :size="17" />
                        </Link>
                    </div>
                </template>

                <template v-else>
                    <div class="dashboard-section-heading">
                        <div>
                            <h3>Your school information</h3>
                            <p>Your personal details and class assignment.</p>
                        </div>
                    </div>
                    <div class="dashboard-stats dashboard-student-stats">
                        <StatCard label="My name" :value="student?.name ?? page.props.auth.user.name"
                            detail="Student profile" icon="profile" />
                        <StatCard label="My class" :value="classLabel(student?.school_class)" detail="Class assignment"
                            icon="classes" tone="violet" />
                        <StatCard label="My teacher" :value="classTeacher ?? 'Not assigned'" detail="Class teacher"
                            icon="teachers" tone="blue" />
                    </div>
                    <div v-if="!student" class="dashboard-unlinked">Your account is not linked to a student record yet.
                        Please contact the Principal.</div>
                    <div class="dashboard-section-heading">
                        <div>
                            <h3>Your profile</h3>
                            <p>Review the contact and class details associated with you.</p>
                        </div>
                    </div>
                    <Link :href="route('student.profile')" class="dashboard-action-card dashboard-link-card">
                        <span class="dashboard-action-icon">
                            <Icon name="profile" />
                        </span>
                        <span class="dashboard-action-copy"><strong>View my profile</strong><small>Your personal and
                                class information.</small></span>
                        <Icon class="dashboard-action-arrow" name="arrow" :size="17" />
                    </Link>
                </template>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
