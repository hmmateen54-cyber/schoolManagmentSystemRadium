<script setup>
import Icon from '@/Components/Icon.vue';

defineProps({
    title: { type: String, required: true },
    description: { type: String, default: '' },
    icon: { type: String, default: 'book' },
});
</script>

<template>
    <div class="app-empty-state">
        <span class="app-empty-state-icon"><Icon :name="icon" :size="19" /></span>
        <strong>{{ title }}</strong>
        <p v-if="description">{{ description }}</p>
    </div>
</template>
