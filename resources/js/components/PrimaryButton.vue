<script setup lang="ts">
defineProps<{
    type?: 'submit' | 'button' | 'reset';
    disabled?: boolean;
}>();
</script>

<template>
    <button
        :type="type || 'submit'"
        :disabled="disabled"
        class="inline-flex cursor-pointer items-center rounded-md border border-transparent bg-blue-600 px-4 py-2 text-xs font-semibold tracking-widest text-white uppercase transition duration-150 ease-in-out hover:bg-blue-700 focus:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:outline-none active:bg-blue-900 disabled:cursor-not-allowed disabled:opacity-50"
    >
        <slot />
    </button>
</template>
