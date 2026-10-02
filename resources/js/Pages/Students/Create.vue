<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

const props = defineProps({
    schoolClasses: Array,
    accountOptions: Array,
})

const form = useForm({
    name: '',
    email: '',
    phone: '',
    age: '',
    class_id: '',
    user_id: '',
})

const classLabel = (schoolClass) => schoolClass.section
    ? `${schoolClass.name}-${schoolClass.section}`
    : schoolClass.name

const submit = () => form.post(route('students.store'))
</script>

<template>
    <Head title="Add Student" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Add Student</h2>
        </template>

        <div class="py-12">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <div v-if="props.schoolClasses.length === 0" class="mb-6 rounded bg-amber-50 p-4 text-amber-900">
                        Create a class before adding students.
                        <Link :href="route('classes.create')" class="font-semibold underline">Create a class</Link>
                    </div>

                    <form @submit.prevent="submit" class="app-form-grid">
                        <div>
                            <label for="student-name" class="block text-sm font-medium text-gray-700">Name</label>
                            <input id="student-name" v-model="form.name" type="text" required placeholder="e.g. Amina Khan" class="mt-1 block w-full rounded-md border-gray-300" />
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
                            <input id="student-age" v-model="form.age" type="number" min="3" max="30" required placeholder="Age in years" class="mt-1 block w-full rounded-md border-gray-300" />
                            <p v-if="form.errors.age" class="mt-1 text-sm text-red-600">{{ form.errors.age }}</p>
                        </div>

                        <div>
                            <label for="student-class" class="block text-sm font-medium text-gray-700">Class</label>
                            <select id="student-class" v-model="form.class_id" required class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">Select a class</option>
                                <option v-for="schoolClass in props.schoolClasses" :key="schoolClass.id" :value="schoolClass.id">
                                    {{ classLabel(schoolClass) }}
                                </option>
                            </select>
                            <p v-if="form.errors.class_id" class="mt-1 text-sm text-red-600">{{ form.errors.class_id }}</p>
                        </div>

                        <div>
                            <label for="student-account" class="block text-sm font-medium text-gray-700">Login account (optional)</label>
                            <select id="student-account" v-model="form.user_id" class="mt-1 block w-full rounded-md border-gray-300">
                                <option value="">No account linked</option>
                                <option v-for="account in props.accountOptions" :key="account.id" :value="account.id">
                                    {{ account.name }} — {{ account.email }}
                                </option>
                            </select>
                            <p v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">{{ form.errors.user_id }}</p>
                            <p class="mt-1 text-sm text-gray-500">Accounts are created through registration and linked here by the Principal.</p>
                        </div>

                        <div class="app-form-actions">
                            <button type="submit" :disabled="form.processing || props.schoolClasses.length === 0" class="rounded bg-blue-600 px-4 py-2 text-white disabled:opacity-50">
                                {{ form.processing ? 'Saving...' : 'Save Student' }}
                            </button>
                            <Link :href="route('students.index')" class="rounded bg-gray-500 px-4 py-2 text-white">Cancel</Link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
