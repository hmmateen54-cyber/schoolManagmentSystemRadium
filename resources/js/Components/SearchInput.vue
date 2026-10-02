<script setup>
import Icon from '@/Components/Icon.vue';

defineProps({
    label: { type: String, required: true },
    placeholder: { type: String, default: 'Search…' },
});

const model = defineModel({ type: String, default: '' });
</script>

<template>
    <label class="app-search-field">
        <Icon name="search" :size="17" />
        <input v-model="model" type="search" :aria-label="label" :placeholder="placeholder" />
    </label>
</template>
