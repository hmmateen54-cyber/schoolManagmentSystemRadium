<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import Badge from '@/Components/Badge.vue'
import EmptyState from '@/Components/EmptyState.vue'

defineProps({ schoolClass: Object })

const classLabel = (value) => value?.section
    ? `${value.name}-${value.section}`
    : value?.name || 'No class assigned'
</script>

<template>
    <Head title="My Class" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Class</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div v-if="!schoolClass" class="rounded-lg bg-amber-50 p-6 text-amber-900">
                    Your account is not linked to a teacher record or assigned class. Please contact the Principal.
                </div>
                <div v-else class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="mb-6 flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <Badge tone="teal">Assigned class</Badge>
                            <h3 class="text-xl font-semibold">{{ classLabel(schoolClass) }}</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ schoolClass.students.length }} students</p>
                        </div>
                        <Link :href="route('students.index')" class="rounded bg-blue-600 px-4 py-2 text-white">Search My Students</Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border">
                            <thead><tr class="bg-gray-100"><th class="border p-3 text-left">Name</th><th class="border p-3 text-left">Email</th><th class="border p-3 text-left">Details</th></tr></thead>
                            <tbody>
                                <tr v-for="student in schoolClass.students" :key="student.id">
                                    <td class="border p-3"><strong class="student-table-name">{{ student.name }}</strong></td>
                                    <td class="border p-3">{{ student.email }}</td>
                                    <td class="border p-3"><Link :href="route('students.show', student.id)" class="text-blue-700 underline">View</Link></td>
                                </tr>
                                <tr v-if="schoolClass.students.length === 0"><td colspan="3"><EmptyState title="No students in this class yet" description="Students assigned by the Principal will appear here." icon="students" /></td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
