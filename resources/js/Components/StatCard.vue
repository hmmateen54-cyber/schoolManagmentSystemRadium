<script setup>
import Icon from '@/Components/Icon.vue';

defineProps({
    label: { type: String, required: true },
    value: { type: [String, Number], required: true },
    detail: { type: String, required: true },
    icon: { type: String, default: 'chart' },
    tone: { type: String, default: 'teal' },
});
</script>

<template>
    <article class="stat-card" :class="`stat-card-${tone}`">
        <div class="stat-card-top"><span class="stat-card-label">{{ label }}</span><span class="stat-card-icon"><Icon :name="icon" :size="20" /></span></div>
        <div class="stat-card-value">{{ value }}</div>
        <div class="stat-card-detail"><span class="stat-card-indicator"><Icon name="check" :size="13" /></span>{{ detail }}</div>
        <span class="stat-card-accent" />
    </article>
</template>
