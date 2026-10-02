<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'

defineProps({ accountOptions: Array })

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    user_id: '',
})

const submit = () => {
    form.post(route('teachers.store'))
}
</script>

<template>

    <Head title="Add Teacher" />

    <AuthenticatedLayout>

        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Teacher
            </h2>
        </template>

        <div class="py-12">

            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

                <div class="bg-white p-6 shadow-sm sm:rounded-lg">

                    <form @submit.prevent="submit" class="app-form-grid">

                        <!-- Name -->

                        <div class="mb-4">

                            <label for="teacher-name" class="block font-medium text-sm text-gray-700">
                                Name
                            </label>

                            <input id="teacher-name"
                                v-model="form.name"
                                type="text"
                                placeholder="e.g. Ali Ahmed"
                                class="mt-1 block w-full border-gray-300 rounded-md"
                            />

                            <div
                                v-if="form.errors.name"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.name }}
                            </div>

                        </div>

                        <!-- Email -->

                        <div class="mb-4">

                            <label for="teacher-email" class="block font-medium text-sm text-gray-700">
                                Email
                            </label>

                            <input id="teacher-email"
                                v-model="form.email"
                                type="email"
                                placeholder="teacher@example.com"
                                class="mt-1 block w-full border-gray-300 rounded-md"
                            />

                            <div
                                v-if="form.errors.email"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.email }}
                            </div>

                        </div>

                        <!-- Phone -->

                        <div class="mb-4">

                            <label for="teacher-phone" class="block font-medium text-sm text-gray-700">
                                Phone
                            </label>

                            <input id="teacher-phone"
                                v-model="form.phone"
                                type="tel"
                                placeholder="Optional contact number"
                                class="mt-1 block w-full border-gray-300 rounded-md"
                            />

                            <div
                                v-if="form.errors.phone"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.phone }}
                            </div>

                        </div>

                        <!-- Subject -->

                        <div class="mb-6">

                            <label for="teacher-subject" class="block font-medium text-sm text-gray-700">
                                Subject
                            </label>

                            <input id="teacher-subject"
                                v-model="form.subject"
                                type="text"
                                placeholder="e.g. Mathematics"
                                class="mt-1 block w-full border-gray-300 rounded-md"
                            />

                            <div
                                v-if="form.errors.subject"
                                class="text-red-500 text-sm mt-1"
                            >
                                {{ form.errors.subject }}
                            </div>

                        </div>

                        <div class="mb-6">
                            <label for="teacher-account" class="block font-medium text-sm text-gray-700">
                                Login account (optional)
                            </label>
                            <select
                                id="teacher-account"
                                v-model="form.user_id"
                                class="mt-1 block w-full border-gray-300 rounded-md"
                            >
                                <option value="">No account linked</option>
                                <option v-for="account in accountOptions" :key="account.id" :value="account.id">
                                    {{ account.name }} — {{ account.email }}
                                </option>
                            </select>
                            <div v-if="form.errors.user_id" class="mt-1 text-sm text-red-600">
                                {{ form.errors.user_id }}
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                Link an existing registered account. Its role will become Teacher.
                            </p>
                        </div>

                        <!-- Buttons -->

                        <div class="app-form-actions">

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="px-4 py-2 bg-blue-600 text-white rounded"
                            >
                                {{ form.processing ? 'Saving...' : 'Save Teacher' }}
                            </button>

                            <Link
                                :href="route('teachers.index')"
                                class="px-4 py-2 bg-gray-500 text-white rounded"
                            >
                                Cancel
                            </Link>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </AuthenticatedLayout>

</template>
