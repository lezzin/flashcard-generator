<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps<{
    type?: 'success' | 'error' | 'info';
}>();

const classes = computed(() => {
    return {
        'bg-green-100 text-green-800': props.type === 'success',
        'bg-red-100 text-red-800': props.type === 'error',
        'bg-blue-100 text-blue-800': props.type === 'info' || !props.type,
    };
});
</script>

<template>
    <div
        :class="[
            'rounded-md border border-transparent px-4 py-3 text-sm font-medium shadow-sm',
            classes,
        ]"
    >
        <slot />
    </div>
</template>
