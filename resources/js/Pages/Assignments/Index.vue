<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    assignments: {
        type: Array,
        default: () => [],
    },
    classes: {
        type: Array,
        default: () => [],
    },
})

const editingId = ref(null)

const form = useForm({
    class_id: '',
    subject: '',
    title: '',
    description: '',
    type: 'homework',
    assigned_date: '',
    due_date: '',
})

const submit = () => {
    if (editingId.value) {
        form.put(route('teacher.assignments.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => {
                cancelEdit()
            },
        })

        return
    }

    form.post(route('teacher.assignments.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset(
                'class_id',
                'subject',
                'title',
                'description',
                'assigned_date',
                'due_date'
            )

            form.type = 'homework'
        },
    })
}

const startEdit = (assignment) => {
    editingId.value = assignment.id

    form.class_id = assignment.class_id
    form.subject = assignment.subject
    form.title = assignment.title
    form.description = assignment.description ?? ''
    form.type = assignment.type
    form.assigned_date = assignment.assigned_date
        ? String(assignment.assigned_date).slice(0, 10)
        : ''
    form.due_date = assignment.due_date
        ? String(assignment.due_date).slice(0, 10)
        : ''

    form.clearErrors()

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    })
}

const cancelEdit = () => {
    editingId.value = null

    form.reset(
        'class_id',
        'subject',
        'title',
        'description',
        'assigned_date',
        'due_date'
    )

    form.type = 'homework'
    form.clearErrors()
}

const deleteAssignment = (id) => {
    if (!confirm('Kya aap ye assignment delete karna chahte hain?')) {
        return
    }

    form.delete(route('teacher.assignments.destroy', id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Classwork & Homework" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Classwork & Homework
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Apni class ke liye classwork aur homework create karein.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Create / Edit Assignment -->
                <div
                    class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100"
                >
                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ editingId ? 'Edit Work' : 'Create Work' }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                {{
                                    editingId
                                        ? 'Existing classwork ya homework update karein.'
                                        : 'Students ko dene ke liye classwork ya homework add karein.'
                                }}
                            </p>
                        </div>

                        <button
                            v-if="editingId"
                            type="button"
                            @click="cancelEdit"
                            class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                    </div>

                    <form @submit.prevent="submit">
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">

                            <!-- Class -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Class
                                </label>

                                <select
                                    v-model="form.class_id"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">
                                        Select Class
                                    </option>

                                    <option
                                        v-for="schoolClass in classes"
                                        :key="schoolClass.id"
                                        :value="schoolClass.id"
                                    >
                                        {{ schoolClass.name }}

                                        <template v-if="schoolClass.section">
                                            - {{ schoolClass.section }}
                                        </template>
                                    </option>
                                </select>

                                <p
                                    v-if="form.errors.class_id"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.class_id }}
                                </p>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Subject
                                </label>

                                <input
                                    v-model="form.subject"
                                    type="text"
                                    placeholder="e.g. Botany"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />

                                <p
                                    v-if="form.errors.subject"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.subject }}
                                </p>
                            </div>

                            <!-- Type -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Type
                                </label>

                                <select
                                    v-model="form.type"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="homework">
                                        Homework
                                    </option>

                                    <option value="classwork">
                                        Classwork
                                    </option>
                                </select>

                                <p
                                    v-if="form.errors.type"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.type }}
                                </p>
                            </div>

                            <!-- Title -->
                            <div class="md:col-span-2">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Title
                                </label>

                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="e.g. Chapter 1 Questions"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />

                                <p
                                    v-if="form.errors.title"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="lg:col-span-3">
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Description
                                </label>

                                <textarea
                                    v-model="form.description"
                                    rows="4"
                                    placeholder="Students ko kya karna hai?"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                ></textarea>

                                <p
                                    v-if="form.errors.description"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.description }}
                                </p>
                            </div>

                            <!-- Assigned Date -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Assigned Date
                                </label>

                                <input
                                    v-model="form.assigned_date"
                                    type="date"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />

                                <p
                                    v-if="form.errors.assigned_date"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.assigned_date }}
                                </p>
                            </div>

                            <!-- Due Date -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Due Date
                                </label>

                                <input
                                    v-model="form.due_date"
                                    type="date"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />

                                <p
                                    v-if="form.errors.due_date"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.due_date }}
                                </p>
                            </div>

                            <!-- Submit -->
                            <div class="flex items-end">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="w-full rounded-xl bg-indigo-600 px-5 py-3 font-semibold text-white transition hover:bg-indigo-700 disabled:cursor-not-allowed disabled:opacity-50"
                                >
                                    {{
                                        form.processing
                                            ? 'Saving...'
                                            : editingId
                                                ? 'Update Work'
                                                : 'Create Work'
                                    }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Assignment List -->
                <div
                    class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100"
                >
                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            My Work
                        </h3>
                    </div>

                    <div
                        v-if="assignments.length === 0"
                        class="px-6 py-12 text-center text-gray-500"
                    >
                        Abhi koi classwork ya homework create nahi kiya gaya.
                    </div>

                    <div
                        v-else
                        class="overflow-x-auto"
                    >
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Type
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Class
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Subject
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Title
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Due Date
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">
                                <tr
                                    v-for="assignment in assignments"
                                    :key="assignment.id"
                                    class="hover:bg-gray-50"
                                >
                                    <td class="px-6 py-4">
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
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ assignment.school_class?.name }}

                                        <span
                                            v-if="assignment.school_class?.section"
                                        >
                                            - {{ assignment.school_class.section }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ assignment.subject }}
                                    </td>

                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ assignment.title }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ assignment.due_date ?? 'No due date' }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">
                                            <button
                                                type="button"
                                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                                                @click="startEdit(assignment)"
                                            >
                                                Edit
                                            </button>

                                            <button
                                                type="button"
                                                class="text-sm font-semibold text-red-600 hover:text-red-800"
                                                @click="deleteAssignment(assignment.id)"
                                            >
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>