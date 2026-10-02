<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({ teachers: Array })

const form = useForm({ name: '', section: '', teacher_id: '' })
const submit = () => form.post(route('classes.store'))
</script>

<template>
    <Head title="Add Class" />
    <AuthenticatedLayout>
        <template #header><h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Class</h2></template>
        <div class="py-12">
            <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="app-form-grid rounded-lg bg-white p-6 shadow-sm">
                    <div>
                        <label for="class-name" class="block text-sm font-medium text-gray-700">Class name</label>
                        <input id="class-name" v-model="form.name" required maxlength="255" placeholder="e.g. Class 5" class="mt-1 block w-full rounded-md border-gray-300" />
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label for="class-section" class="block text-sm font-medium text-gray-700">Section (optional)</label>
                        <input id="class-section" v-model="form.section" maxlength="50" placeholder="e.g. A" class="mt-1 block w-full rounded-md border-gray-300" />
                        <p v-if="form.errors.section" class="mt-1 text-sm text-red-600">{{ form.errors.section }}</p>
                    </div>
                    <div>
                        <label for="class-teacher" class="block text-sm font-medium text-gray-700">Assigned teacher (optional)</label>
                        <select id="class-teacher" v-model="form.teacher_id" class="mt-1 block w-full rounded-md border-gray-300">
                            <option value="">No teacher assigned</option>
                            <option v-for="teacher in teachers" :key="teacher.id" :value="teacher.id">{{ teacher.name }} — {{ teacher.subject }}</option>
                        </select>
                        <p v-if="form.errors.teacher_id" class="mt-1 text-sm text-red-600">{{ form.errors.teacher_id }}</p>
                    </div>
                    <div class="app-form-actions">
                        <button type="submit" :disabled="form.processing" class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50">{{ form.processing ? 'Saving...' : 'Save Class' }}</button>
                        <Link :href="route('classes.index')" class="rounded bg-gray-500 px-4 py-2 text-white">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
