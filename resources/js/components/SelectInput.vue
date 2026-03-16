<script setup lang="ts">
defineProps<{
    modelValue: string;
}>();

defineEmits(['update:modelValue']);
</script>

<template>
    <select
        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full px-3 py-2 text-sm"
        :value="modelValue"
        @change="$emit('update:modelValue', ($event.target as HTMLSelectElement).value)"
    >
        <slot />
    </select>
</template>
