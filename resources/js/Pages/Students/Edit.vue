<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'

const props = defineProps({
    student: Object,
    schoolClasses: {
        type: Array,
        default: () => [],
    },
    accountOptions: {
        type: Array,
        default: () => [],
    },
})

const isPrincipal = usePage().props.auth.user.role === 'principal'

const form = useForm({
    name: props.student.name,
    email: props.student.email,
    phone: props.student.phone ?? '',
    age: props.student.age,
    class_id: props.student.class_id ?? '',
    user_id: props.student.user_id ?? '',
})

const classLabel = (schoolClass) => schoolClass.section
    ? `${schoolClass.name}-${schoolClass.section}`
    : schoolClass.name

const submit = () => form.put(route('students.update', props.student.id))
</script>

<template>
    <Head title="Edit Student" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Student</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="app-form-grid">
                        <div>
                            <label for="student-name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input id="student-name" v-model="form.name" type="text" required placeholder="Student full name" class="mt-1 block w-full rounded-md border-gray-300" />
                            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                        </div>

                        <div>
                            <label for="student-email" class="block text-sm font-medium text-gray-700">Email</label>
                            <input id="student-email" v-model="form.email" type="email" required placeholder="student@example.com" class="mt-1 block w-full rounded-md border-gray-300" />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <label for="student-phone" class="block text-sm font-medium text-gray-700">Phone</label>
                            <input id="student-phone" v-model="form.phone" type="tel" placeholder="Optional contact number" class="mt-1 block w-full rounded-md border-gray-300" />
                            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                        </div>

                        <div>
                            <label for="student-age" class="block text-sm font-medium text-gray-700">Age</label>
                            <input id="student-age" v-model="form.age" type="number" min="3" max="30" required class="mt-1 block w-full rounded-md border-gray-300" />
                            <p v-if="form.errors.age" class="mt-1 text-sm text-red-600">{{ form.errors.age }}</p>
                        </div>

                        <div v-if="isPrincipal">
                            <label for="student-class" class="block text-sm font-medium text-gray-700">Class</label>
                            <select id="student-class" v-model="form.class_id" required class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Select a class</option>
                                <option v-for="schoolClass in props.schoolClasses" :key="schoolClass.id" :value="schoolClass.id">
                                    {{ classLabel(schoolClass) }}
                                </option>
                            </select>
                            <p v-if="form.errors.class_id" class="mt-1 text-sm text-red-600">{{ form.errors.class_id }}</p>
                        </div>
                        <p v-else class="text-sm text-gray-600">Class: {{ classLabel(student.school_class) }}</p>

                        <div v-if="isPrincipal">
                            <label for="student-account" class="block text-sm font-medium text-gray-700">Login account (optional)</label>
                            <select id="student-account" v-model="form.user_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">No account linked</option>
                                <option v-for="account in props.accountOptions" :key="account.id" :value="account.id">
                                    {{ account.name }} — {{ account.email }}
                                </option>
                            </select>
                            <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">{{ form.errors.user_id }}</p>
                        </div>

                        <div class="app-form-actions">
                            <button type="submit" :disabled="form.processing" class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50">
                                {{ form.processing ? 'Updating...' : 'Update Student' }}
                            </button>
                            <Link :href="route('students.index')" class="rounded bg-gray-500 px-4 py-2 text-white">Cancel</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
