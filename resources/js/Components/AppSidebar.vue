<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

const props = defineProps({ open: { type: Boolean, default: false } });
const emit = defineEmits(['close']);
const page = usePage();
const user = computed(() => page.props.auth.user);
const role = computed(() => user.value?.role ?? '');
const roleLabel = computed(() => ({ principal: 'Principal', teacher: 'Teacher', student: 'Student' }[role.value] ?? 'Member'));
const initials = computed(() => (user.value?.name ?? 'School').split(/\s+/).slice(0, 2).map((part) => part[0]).join('').toUpperCase());

const links = computed(() => {
    const overview = [{ label: 'Overview', route: 'dashboard', icon: 'dashboard' }];

    if (role.value === 'principal') {
        return [...overview, { label: 'Students', route: 'students.index', icon: 'students' }, { label: 'Teachers', route: 'teachers.index', icon: 'teachers' }, { label: 'Classes', route: 'classes.index', icon: 'classes' }];
    }

    if (role.value === 'teacher') {
        return [
            ...overview,
            { label: 'My Class', route: 'teacher.class', icon: 'classes' },
            { label: 'Students', route: 'students.index', icon: 'students' },
            { label: 'My Schedule', route: 'teacher.schedules.index', icon: 'classes' },
            { label: 'Classwork & Homework', route: 'teacher.assignments.index', icon: 'profile' },
        ];
    }

    if (role.value === 'student') {
        return [...overview, { label: 'My Profile', route: 'student.profile', icon: 'profile' }];
    }

    return overview;
});

const isActive = (name) => route().current(name);
</script>

<template>
    <aside class="app-sidebar app-sidebar-desktop" aria-label="Main navigation">
        <Link :href="route('dashboard')" class="app-brand" aria-label="Schoolhouse home">
            <span class="app-brand-mark"><Icon name="school" :size="23" /></span>
            <span class="app-brand-copy"><strong>Schoolhouse</strong><small>Management Suite</small></span>
        </Link>

        <div class="app-sidebar-caption">WORKSPACE</div>
        <nav class="app-sidebar-nav">
            <Link v-for="item in links" :key="item.route" :href="route(item.route)" class="app-sidebar-link" :class="{ 'is-active': isActive(item.route) }">
                <Icon :name="item.icon" :size="19" />
                <span>{{ item.label }}</span>
                <span v-if="isActive(item.route)" class="app-sidebar-active-mark" />
            </Link>
        </nav>

        <div class="app-sidebar-spacer" />
        <div class="app-sidebar-note">
            <span class="app-sidebar-note-icon"><Icon name="shield" :size="17" /></span>
            <div><strong>School workspace</strong><span>Secure and up to date</span></div>
            <span class="app-online-dot" aria-label="Online" />
        </div>
        <div class="app-sidebar-user">
            <span class="app-avatar">{{ initials }}</span>
            <span class="app-sidebar-user-copy"><strong>{{ user?.name }}</strong><small>{{ user?.email }}</small></span>
            <span class="app-role-pill">{{ roleLabel }}</span>
        </div>
    </aside>

    <Transition name="app-drawer">
        <div v-if="open" class="app-mobile-drawer-wrap lg:hidden">
            <button class="app-mobile-backdrop" type="button" aria-label="Close navigation" @click="emit('close')" />
            <aside class="app-sidebar app-sidebar-mobile" aria-label="Main navigation">
                <div class="app-mobile-sidebar-top">
                    <Link :href="route('dashboard')" class="app-brand" @click="emit('close')">
                        <span class="app-brand-mark"><Icon name="school" :size="23" /></span>
                        <span class="app-brand-copy"><strong>Schoolhouse</strong><small>Management Suite</small></span>
                    </Link>
                    <button class="app-icon-button app-close-button" type="button" aria-label="Close navigation" @click="emit('close')"><Icon name="close" /></button>
                </div>
                <div class="app-sidebar-caption">WORKSPACE</div>
                <nav class="app-sidebar-nav">
                    <Link v-for="item in links" :key="item.route" :href="route(item.route)" class="app-sidebar-link" :class="{ 'is-active': isActive(item.route) }" @click="emit('close')">
                        <Icon :name="item.icon" :size="19" /><span>{{ item.label }}</span><span v-if="isActive(item.route)" class="app-sidebar-active-mark" />
                    </Link>
                </nav>
                <div class="app-sidebar-spacer" />
                <div class="app-sidebar-user">
                    <span class="app-avatar">{{ initials }}</span>
                    <span class="app-sidebar-user-copy"><strong>{{ user?.name }}</strong><small>{{ user?.email }}</small></span>
                    <span class="app-role-pill">{{ roleLabel }}</span>
                </div>
            </aside>
        </div>
    </Transition>
</template>
