<script setup lang="ts">
import { onMounted, ref } from 'vue';

defineProps<{
    modelValue: string;
}>();

defineEmits(['update:modelValue']);

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value?.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:ring-blue-500"
        :value="modelValue" @input="
            $emit(
                'update:modelValue',
                ($event.target as HTMLInputElement).value,
            )
            " ref="input" />
</template>
