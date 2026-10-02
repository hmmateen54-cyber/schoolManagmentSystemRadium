<script setup>
import { computed } from 'vue';

const props = defineProps({
    name: { type: String, required: true },
    size: { type: [String, Number], default: 20 },
});

const paths = {
    school: ['M3 10.5 12 5l9 5.5', 'M5 9.5v9h14v-9', 'M9 18.5v-5h6v5', 'M3 21h18'],
    dashboard: ['M4 4h6v6H4z', 'M14 4h6v6h-6z', 'M14 14h6v6h-6z', 'M4 14h6v6H4z'],
    students: ['M16 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2', 'M10 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z', 'M20 21v-2a4 4 0 0 0-3-3.87', 'M16 3.13a4 4 0 0 1 0 7.75'],
    teachers: ['M4 7.5h16v13H4z', 'M8 7.5V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2.5', 'M4 12h16', 'M10 12v2h4v-2'],
    classes: ['M4 5.5h16v14H4z', 'M8 3v5', 'M16 3v5', 'M4 10h16', 'M8 14h3', 'M8 17h6'],
    profile: ['M20 21a8 8 0 0 0-16 0', 'M12 13a5 5 0 1 0 0-10 5 5 0 0 0 0 10z'],
    logout: ['M10 17l5-5-5-5', 'M15 12H3', 'M12 3h6a3 3 0 0 1 3 3v12a3 3 0 0 1-3 3h-6'],
    menu: ['M4 6h16', 'M4 12h16', 'M4 18h16'],
    close: ['M18 6 6 18', 'm6 6 12 12'],
    chevron: ['m9 18 6-6-6-6'],
    arrow: ['M7 17 17 7', 'M7 7h10v10'],
    search: ['m20 20-4.35-4.35', 'M18 10.5a7.5 7.5 0 1 1-15 0 7.5 7.5 0 0 1 15 0z'],
    book: ['M4 5.5A2.5 2.5 0 0 1 6.5 3H20v16H6.5A2.5 2.5 0 0 0 4 21z', 'M4 5.5v15.5', 'M8 7h8', 'M8 11h8'],
    chart: ['M4 19V5', 'M4 19h17', 'm7 15 4-4 3 2 6-7'],
    shield: ['M12 22s8-4 8-11V5l-8-3-8 3v6c0 7 8 11 8 11z', 'm9 12 2 2 4-4'],
    check: ['m5 12 4 4L19 6'],
};

const iconPaths = computed(() => paths[props.name] ?? paths.dashboard);
</script>

<template>
    <svg :width="size" :height="size" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
        <path v-for="(path, index) in iconPaths" :key="index" :d="path" />
    </svg>
</template>
