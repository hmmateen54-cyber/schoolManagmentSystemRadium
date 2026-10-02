<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'

defineProps({
    student: Object,
    assignments: {
        type: Array,
        default: () => [],
    },
})

const classLabel = (schoolClass, legacyClass = null) => {
    if (!schoolClass) return legacyClass || 'Not assigned'

    return schoolClass.section
        ? `${schoolClass.name}-${schoolClass.section}`
        : schoolClass.name
}

const formatDate = (date) => {
    if (!date) return '—'

    return new Date(date).toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    })
}
</script>

<template>
    <Head title="My Profile" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                My Information
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-5xl space-y-6 sm:px-6 lg:px-8">

                <!-- Student Information -->
                <div
                    v-if="!student"
                    class="rounded-lg bg-amber-50 p-6 text-amber-900"
                >
                    Your login account is not linked to a student record yet.
                    Please contact the Principal.
                </div>

                <div
                    v-else
                    class="rounded-lg bg-white p-6 shadow-sm"
                >
                    <h3 class="mb-5 text-lg font-semibold text-gray-900">
                        My Information
                    </h3>

                    <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm text-gray-500">Name</dt>
                            <dd class="mt-1 font-medium">{{ student.name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Email</dt>
                            <dd class="mt-1 font-medium">{{ student.email }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Phone</dt>
                            <dd class="mt-1 font-medium">
                                {{ student.phone || '—' }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Age</dt>
                            <dd class="mt-1 font-medium">{{ student.age }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Class</dt>
                            <dd class="mt-1 font-medium">
                                {{ classLabel(student.school_class, student.class) }}
                            </dd>
                        </div>

                        <div>
                            <dt class="text-sm text-gray-500">Teacher</dt>
                            <dd class="mt-1 font-medium">
                                {{ student.school_class?.teacher?.name || 'Not assigned' }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <!-- Classwork & Homework -->
                <div
                    v-if="student"
                    class="overflow-hidden rounded-lg bg-white shadow-sm"
                >
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Classwork & Homework
                        </h3>

                        <p class="mt-1 text-sm text-gray-500">
                            Aapki class ke liye teacher ne jo work diya hai.
                        </p>
                    </div>

                    <!-- No assignments -->
                    <div
                        v-if="assignments.length === 0"
                        class="px-6 py-10 text-center text-gray-500"
                    >
                        Abhi aapki class ke liye koi classwork ya homework nahi diya gaya.
                    </div>

                    <!-- Assignments -->
                    <div
                        v-else
                        class="divide-y divide-gray-100"
                    >
                        <div
                            v-for="assignment in assignments"
                            :key="assignment.id"
                            class="p-6 transition hover:bg-gray-50"
                        >
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                                <div class="flex-1">
                                    <div class="mb-2 flex flex-wrap items-center gap-2">
                                        <span
                                            class="rounded-full px-3 py-1 text-xs font-semibold capitalize"
                                            :class="
                                                assignment.type === 'homework'
                                                    ? 'bg-amber-100 text-amber-700'
                                                    : 'bg-blue-100 text-blue-700'
                                            "
                                        >
                                            {{ assignment.type }}
                                        </span>

                                        <span class="text-sm text-gray-500">
                                            {{ assignment.subject }}
                                        </span>
                                    </div>

                                    <h4 class="text-base font-semibold text-gray-900">
                                        {{ assignment.title }}
                                    </h4>

                                    <p
                                        v-if="assignment.description"
                                        class="mt-2 whitespace-pre-line text-sm leading-6 text-gray-600"
                                    >
                                        {{ assignment.description }}
                                    </p>
                                </div>

                                <div class="shrink-0 text-sm sm:text-right">
                                    <p class="text-gray-500">
                                        Assigned
                                    </p>

                                    <p class="font-medium text-gray-800">
                                        {{ formatDate(assignment.assigned_date) }}
                                    </p>

                                    <div class="mt-3">
                                        <p class="text-gray-500">
                                            Due
                                        </p>

                                        <p
                                            class="font-medium"
                                            :class="
                                                assignment.due_date
                                                    ? 'text-red-600'
                                                    : 'text-gray-800'
                                            "
                                        >
                                            {{ formatDate(assignment.due_date) }}
                                        </p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>