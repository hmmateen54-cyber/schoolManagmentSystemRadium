<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'

const props = defineProps({
    schedules: {
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
    day: '',
    start_time: '',
    end_time: '',
})

const submit = () => {
    if (editingId.value) {
        form.put(route('teacher.schedules.update', editingId.value), {
            preserveScroll: true,
            onSuccess: () => {
                cancelEdit()
            },
        })

        return
    }

    form.post(route('teacher.schedules.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
        },
    })
}

const startEdit = (schedule) => {
    editingId.value = schedule.id

    form.class_id = schedule.class_id
    form.subject = schedule.subject
    form.day = schedule.day
    form.start_time = schedule.start_time?.slice(0, 5) ?? ''
    form.end_time = schedule.end_time?.slice(0, 5) ?? ''

    window.scrollTo({
        top: 0,
        behavior: 'smooth',
    })
}

const cancelEdit = () => {
    editingId.value = null
    form.reset()
    form.clearErrors()
}

const deleteSchedule = (id) => {
    if (!confirm('Kya aap ye period delete karna chahte hain?')) {
        return
    }

    form.delete(route('teacher.schedules.destroy', id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="My Schedule" />

    <AuthenticatedLayout>
        <template #header>
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    My Teaching Schedule
                </h2>

                <p class="mt-1 text-sm text-gray-500">
                    Apni classes ke periods manage karo.
                </p>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Add / Edit Schedule -->
                <div class="overflow-hidden rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-100">

                    <div class="mb-6 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                {{ editingId ? 'Edit Class Period' : 'Add Class Period' }}
                            </h3>

                            <p class="mt-1 text-sm text-gray-500">
                                {{
                                    editingId
                                        ? 'Existing period ki details update karein.'
                                        : 'Apni class ka time period add karein.'
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

                            <!-- Day -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Day
                                </label>

                                <select
                                    v-model="form.day"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">
                                        Select Day
                                    </option>

                                    <option value="Monday">Monday</option>
                                    <option value="Tuesday">Tuesday</option>
                                    <option value="Wednesday">Wednesday</option>
                                    <option value="Thursday">Thursday</option>
                                    <option value="Friday">Friday</option>
                                    <option value="Saturday">Saturday</option>
                                    <option value="Sunday">Sunday</option>
                                </select>

                                <p
                                    v-if="form.errors.day"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.day }}
                                </p>
                            </div>

                            <!-- Start Time -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    Start Time
                                </label>

                                <input
                                    v-model="form.start_time"
                                    type="time"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />

                                <p
                                    v-if="form.errors.start_time"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.start_time }}
                                </p>
                            </div>

                            <!-- End Time -->
                            <div>
                                <label class="mb-2 block text-sm font-medium text-gray-700">
                                    End Time
                                </label>

                                <input
                                    v-model="form.end_time"
                                    type="time"
                                    class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                />

                                <p
                                    v-if="form.errors.end_time"
                                    class="mt-1 text-sm text-red-600"
                                >
                                    {{ form.errors.end_time }}
                                </p>
                            </div>

                            <!-- Button -->
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
                                                ? 'Update Period'
                                                : 'Add Period'
                                    }}
                                </button>
                            </div>

                        </div>
                    </form>
                </div>

                <!-- Schedule List -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-100">

                    <div class="border-b border-gray-100 px-6 py-5">
                        <h3 class="text-lg font-semibold text-gray-900">
                            My Periods
                        </h3>
                    </div>

                    <div
                        v-if="schedules.length === 0"
                        class="px-6 py-12 text-center text-gray-500"
                    >
                        Abhi koi period add nahi kiya gaya.
                    </div>

                    <div
                        v-else
                        class="overflow-x-auto"
                    >
                        <table class="min-w-full divide-y divide-gray-200">

                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Day
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Time
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Class
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Subject
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                        Action
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100 bg-white">

                                <tr
                                    v-for="schedule in schedules"
                                    :key="schedule.id"
                                    class="hover:bg-gray-50"
                                >

                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                        {{ schedule.day }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ schedule.start_time }} - {{ schedule.end_time }}
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ schedule.school_class?.name }}

                                        <span v-if="schedule.school_class?.section">
                                            - {{ schedule.school_class.section }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ schedule.subject }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-4">

                                            <button
                                                type="button"
                                                class="text-sm font-semibold text-indigo-600 hover:text-indigo-800"
                                                @click="startEdit(schedule)"
                                            >
                                                Edit
                                            </button>

                                            <button
                                                type="button"
                                                class="text-sm font-semibold text-red-600 hover:text-red-800"
                                                @click="deleteSchedule(schedule.id)"
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