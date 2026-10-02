<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import Icon from '@/Components/Icon.vue';

defineEmits(['toggle-navigation']);

const page = usePage();
const user = computed(() => page.props.auth.user);
const roleLabel = computed(() => ({ principal: 'Principal', teacher: 'Teacher', student: 'Student' }[user.value?.role] ?? 'Member'));
const initials = computed(() => (user.value?.name ?? 'School').split(/\s+/).slice(0, 2).map((part) => part[0]).join('').toUpperCase());
</script>

<template>
    <header class="app-topbar">
        <div class="app-topbar-left">
            <button class="app-icon-button app-menu-button lg:hidden" type="button" aria-label="Open navigation" @click="$emit('toggle-navigation')"><Icon name="menu" /></button>
            <div class="app-topbar-titles">
                <div class="app-breadcrumb"><span>Schoolhouse</span><span class="app-breadcrumb-divider">/</span><span>Workspace</span></div>
                <div class="app-topbar-page-title"><slot name="title" /></div>
            </div>
        </div>
        <div class="app-topbar-actions">
            <span class="app-topbar-role"><Icon name="shield" :size="15" />{{ roleLabel }}</span>
            <span class="app-topbar-divider" />
            <Link :href="route('profile.edit')" class="app-topbar-profile" aria-label="Open profile settings">
                <span class="app-avatar app-avatar-small">{{ initials }}</span>
                <span class="app-topbar-user-name">{{ user?.name }}</span>
            </Link>
            <Link :href="route('logout')" method="post" as="button" class="app-icon-button app-logout-button" aria-label="Log out" title="Log out"><Icon name="logout" /><span class="app-logout-label">Log out</span></Link>
        </div>
    </header>
</template>
