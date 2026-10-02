<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

defineProps({ student: Object })
const role = usePage().props.auth.user.role

const classLabel = (schoolClass, legacyClass = null) => {
    if (!schoolClass) return legacyClass || 'Unassigned'
    return schoolClass.section
        ? `${schoolClass.name}-${schoolClass.section}`
        : schoolClass.name
}
</script>

<template>
    <Head :title="`Student: ${student.name}`" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Student Details</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <div class="space-y-4 rounded-lg bg-white p-6 shadow-sm">
                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div><dt class="text-sm text-gray-500">Name</dt><dd class="font-medium">{{ student.name }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Email</dt><dd class="font-medium">{{ student.email }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Phone</dt><dd class="font-medium">{{ student.phone || '—' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Age</dt><dd class="font-medium">{{ student.age }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Class</dt><dd class="font-medium">{{ classLabel(student.school_class, student.class) }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Teacher</dt><dd class="font-medium">{{ student.school_class?.teacher?.name || 'Not assigned' }}</dd></div>
                    </dl>
                    <div class="flex gap-3">
                        <Link v-if="role === 'principal' || role === 'teacher'" :href="route('students.edit', student.id)" class="rounded bg-blue-600 px-4 py-2 text-white">Edit Student</Link>
                        <Link :href="route(role === 'student' ? 'student.profile' : 'students.index')" class="rounded bg-gray-500 px-4 py-2 text-white">
                            {{ role === 'student' ? 'Back to My Profile' : 'Back to Students' }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
