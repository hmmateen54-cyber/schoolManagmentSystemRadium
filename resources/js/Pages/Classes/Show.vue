<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Badge from '@/Components/Badge.vue'
import EmptyState from '@/Components/EmptyState.vue'

defineProps({ schoolClass: Object })

const classLabel = (value) => value.section
    ? `${value.name}-${value.section}`
    : value.name
</script>

<template>
    <Head :title="classLabel(schoolClass)" />
    <AuthenticatedLayout>
        <template #header><h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ classLabel(schoolClass) }}</h2></template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-gray-600">Teacher: <Badge :tone="schoolClass.teacher ? 'teal' : 'slate'">{{ schoolClass.teacher?.name || 'Not assigned' }}</Badge></p>
                            <p class="mt-2 text-gray-600"><Badge tone="slate">{{ schoolClass.students.length }} students</Badge></p>
                        </div>
                        <Link :href="route('classes.edit', schoolClass.id)" class="rounded bg-blue-600 px-4 py-2 text-white">Edit Class</Link>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border">
                            <thead><tr class="bg-gray-100"><th class="border p-3 text-left">Name</th><th class="border p-3 text-left">Email</th><th class="border p-3 text-left">Student details</th></tr></thead>
                            <tbody>
                                <tr v-for="student in schoolClass.students" :key="student.id">
                                    <td class="border p-3">{{ student.name }}</td>
                                    <td class="border p-3">{{ student.email }}</td>
                                    <td class="border p-3"><Link :href="route('students.show', student.id)" class="text-blue-700 underline">View</Link></td>
                                </tr>
                                <tr v-if="schoolClass.students.length === 0"><td colspan="3"><EmptyState title="No students assigned" description="Assign students to this class from the student records." icon="students" /></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <Link :href="route('classes.index')" class="mt-6 inline-block text-blue-700 underline">Back to Classes</Link>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
