<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import SearchInput from '@/Components/SearchInput.vue'
import Badge from '@/Components/Badge.vue'
import EmptyState from '@/Components/EmptyState.vue'

const props = defineProps({
    students: Object,
    filters: Object,
    canCreate: Boolean,
    canEdit: Boolean,
    canDelete: Boolean,
})

const search = ref(props.filters.search ?? '')

const classLabel = (student) => {
    const schoolClass = student.school_class
    if (!schoolClass) return student.class || 'Unassigned'
    return schoolClass.section
        ? `${schoolClass.name}-${schoolClass.section}`
        : schoolClass.name
}

watch(search, (value) => {
    router.get(
        route('students.index'),
        {
            search: value
        },
        {
            preserveState: true,
            replace: true,
            preserveScroll: true
        }
    )
})

const deleteStudent = (id) => {
    if (confirm('Are you sure you want to delete this student?')) {
        router.delete(route('students.destroy', id))
    }
}
</script>
<template>

    <Head title="Students" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Students
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-lg font-semibold">
                            All Students
                        </h3>

                        <SearchInput v-model="search" label="Search students" placeholder="Search students by name, email, or class" />

                        <Link v-if="canCreate" :href="route('students.create')" class="px-4 py-2 bg-blue-600 text-white rounded">
                            Add Student
                        </Link>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full border">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="border p-3 text-left">ID</th>
                                    <th class="border p-3 text-left">Name</th>
                                    <th class="border p-3 text-left">Email</th>
                                    <th class="border p-3 text-left">Phone</th>
                                    <th class="border p-3 text-left">Class</th>
                                    <th class="border p-3 text-left">Age</th>
                                    <th class="border p-3 text-left">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr v-for="student in students.data" :key="student.id">
                                    <td class="border p-3">
                                        {{ student.id }}
                                    </td>

                                    <td class="border p-3">
                                        {{ student.name }}
                                    </td>

                                    <td class="border p-3">
                                        {{ student.email }}
                                    </td>

                                    <td class="border p-3">
                                        {{ student.phone }}
                                    </td>

                                    <td class="border p-3">
                                        <Badge tone="teal">{{ classLabel(student) }}</Badge>
                                    </td>

                                    <td class="border p-3">
                                        {{ student.age }}
                                    </td>

                                    <td class="border p-3">
                                        <Link :href="route('students.show', student.id)" class="text-blue-600 mr-3">
                                            View
                                        </Link>

                                        <Link v-if="canEdit" :href="route('students.edit', student.id)" class="text-blue-600 mr-3">
                                            Edit
                                        </Link>

                                        <button v-if="canDelete" @click="deleteStudent(student.id)" class="text-red-600">
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                <tr v-if="students.data.length === 0">
                                    <td colspan="7"><EmptyState title="No students found" description="Try a different search or add a student to your school." icon="students" /></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Pagination -->
                        <div v-if="students.links && students.links.length > 3" class="mt-6 flex justify-center gap-2">
                            <Link v-for="(link, index) in students.links" :key="index" :href="link.url || '#'"
                                v-html="link.label" :class="[
                                    'px-3 py-2 rounded border',
                                    link.active
                                        ? 'bg-blue-600 text-white'
                                        : 'bg-white text-gray-700',
                                    !link.url
                                        ? 'opacity-50 pointer-events-none'
                                        : ''
                                ]" preserve-scroll />
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
