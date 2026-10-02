<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import AppNavbar from '@/Components/AppNavbar.vue';
import AppSidebar from '@/Components/AppSidebar.vue';

const page = usePage();
const navigationOpen = ref(false);

watch(() => page.url, () => { navigationOpen.value = false; });
</script>

<template>
    <div class="app-frame">
        <AppSidebar :open="navigationOpen" @close="navigationOpen = false" />
        <div class="app-main-shell">
            <AppNavbar @toggle-navigation="navigationOpen = !navigationOpen">
                <template #title><slot name="header" /></template>
            </AppNavbar>
            <main class="app-main">
                <slot />
            </main>
            <footer class="app-footer"><span>Schoolhouse Management Suite</span><span>Thoughtful tools for better school days</span></footer>
        </div>
    </div>
</template>
