<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import Badge from '@/Components/Badge.vue'
import EmptyState from '@/Components/EmptyState.vue'

defineProps({ classes: Object })

const classLabel = (schoolClass) => schoolClass.section
    ? `${schoolClass.name}-${schoolClass.section}`
    : schoolClass.name

const deleteClass = (id) => {
    if (confirm('Delete this class? Students must be moved first.')) {
        router.delete(route('classes.destroy', id))
    }
}
</script>

<template>
    <Head title="Classes" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Classes</h2>
        </template>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="mb-6 flex items-center justify-between gap-4">
                        <h3 class="text-lg font-semibold">School Classes</h3>
                        <Link :href="route('classes.create')" class="rounded bg-blue-600 px-4 py-2 text-white">Add Class</Link>
                    </div>
                    <div v-if="$page.props.errors.class" class="mb-4 rounded bg-red-50 p-3 text-sm text-red-700">
                        {{ $page.props.errors.class }}
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border">
                            <thead><tr class="bg-gray-100"><th class="border p-3 text-left">Class</th><th class="border p-3 text-left">Teacher</th><th class="border p-3 text-left">Students</th><th class="border p-3 text-left">Actions</th></tr></thead>
                            <tbody>
                                <tr v-for="schoolClass in classes.data" :key="schoolClass.id">
                                    <td class="border p-3"><Badge tone="teal">{{ classLabel(schoolClass) }}</Badge></td>
                                    <td class="border p-3">{{ schoolClass.teacher?.name || 'Not assigned' }}</td>
                                    <td class="border p-3">{{ schoolClass.students_count }}</td>
                                    <td class="border p-3">
                                        <Link :href="route('classes.show', schoolClass.id)" class="mr-3 text-blue-700 underline">View</Link>
                                        <Link :href="route('classes.edit', schoolClass.id)" class="mr-3 text-blue-700 underline">Edit</Link>
                                        <button @click="deleteClass(schoolClass.id)" class="text-red-700 underline">Delete</button>
                                    </td>
                                </tr>
                                <tr v-if="classes.data.length === 0"><td colspan="4"><EmptyState title="No classes yet" description="Create a class, then assign its teacher and students." icon="classes" /></td></tr>
                            </tbody>
                        </table>
                    </div>
                    <div v-if="classes.links && classes.links.length > 3" class="mt-6 flex justify-center gap-2">
                        <Link v-for="(link, index) in classes.links" :key="index" :href="link.url || '#'" v-html="link.label" :class="['rounded border px-3 py-2', link.active ? 'bg-blue-600 text-white' : 'bg-white text-gray-700', !link.url ? 'pointer-events-none opacity-50' : '']" preserve-scroll />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
