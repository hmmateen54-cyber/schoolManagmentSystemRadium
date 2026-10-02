<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import SearchInput from '@/Components/SearchInput.vue'
import Badge from '@/Components/Badge.vue'
import EmptyState from '@/Components/EmptyState.vue'

const props = defineProps({
    teachers: Object,
    filters: Object
})

const search = ref(props.filters.search ?? '')

watch(search, (value) => {
    router.get(
        route('teachers.index'),
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

const deleteTeacher = (id) => {
    if (confirm('Are you sure you want to delete this teacher?')) {
        router.delete(route('teachers.destroy', id))
    }
}
</script>

<template>

    <Head title="Teachers" />

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Teachers
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white shadow-sm sm:rounded-lg p-6">

                    <div class="flex justify-between items-center mb-6">

                        <h3 class="text-lg font-semibold">
                            All Teachers
                        </h3>

                        <Link
                            :href="route('teachers.create')"
                            class="px-4 py-2 bg-blue-600 text-white rounded"
                        >
                            Add Teacher
                        </Link>

                    </div>

                    <!-- Live Search -->

                    <div class="mb-6"><SearchInput v-model="search" label="Search teachers" placeholder="Search teachers by name, email, or subject" /></div>

                    <!-- Table -->

                    <div class="overflow-x-auto">

                        <table class="w-full border">

                            <thead>

                                <tr class="bg-gray-100">

                                    <th class="border p-3 text-left">ID</th>
                                    <th class="border p-3 text-left">Name</th>
                                    <th class="border p-3 text-left">Email</th>
                                    <th class="border p-3 text-left">Phone</th>
                                    <th class="border p-3 text-left">Subject</th>
                                    <th class="border p-3 text-left">Class</th>
                                    <th class="border p-3 text-left">Login Account</th>
                                    <th class="border p-3 text-left">Actions</th>

                                </tr>

                            </thead>

                            <tbody>

                                <tr
                                    v-for="teacher in teachers.data"
                                    :key="teacher.id"
                                >

                                    <td class="border p-3">
                                        {{ teacher.id }}
                                    </td>

                                    <td class="border p-3">
                                        {{ teacher.name }}
                                    </td>

                                    <td class="border p-3">
                                        {{ teacher.email }}
                                    </td>

                                    <td class="border p-3">
                                        {{ teacher.phone }}
                                    </td>

                                    <td class="border p-3">
                                        <Badge tone="violet">{{ teacher.subject }}</Badge>
                                    </td>

                                    <td class="border p-3">
                                        <Badge :tone="teacher.school_class ? 'teal' : 'slate'">{{ teacher.school_class
                                            ? (teacher.school_class.section
                                                ? `${teacher.school_class.name}-${teacher.school_class.section}`
                                                : teacher.school_class.name)
                                            : 'Not assigned' }}</Badge>
                                    </td>

                                    <td class="border p-3">
                                        <Badge :tone="teacher.user ? 'green' : 'slate'">{{ teacher.user?.email || 'No account linked' }}</Badge>
                                    </td>

                                    <td class="border p-3">

                                        <Link
                                            :href="route('teachers.edit', teacher.id)"
                                            class="text-blue-600 mr-3"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            @click="deleteTeacher(teacher.id)"
                                            class="text-red-600"
                                        >
                                            Delete
                                        </button>

                                    </td>

                                </tr>

                                <tr v-if="teachers.data.length === 0">

                                    <td colspan="8"><EmptyState title="No teachers found" description="Try a different search or add a teacher profile." icon="teachers" /></td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                    <!-- Pagination -->

                    <div
                        v-if="teachers.links && teachers.links.length > 3"
                        class="mt-6 flex justify-center gap-2"
                    >

                        <Link
                            v-for="(link, index) in teachers.links"
                            :key="index"
                            :href="link.url || '#'"
                            v-html="link.label"
                            :class="[
                                'px-3 py-2 rounded border',
                                link.active
                                    ? 'bg-blue-600 text-white'
                                    : 'bg-white text-gray-700',
                                !link.url
                                    ? 'opacity-50 pointer-events-none'
                                    : ''
                            ]"
                            preserve-scroll
                        />

                    </div>

                </div>

            </div>
        </div>

    </AuthenticatedLayout>

</template>
