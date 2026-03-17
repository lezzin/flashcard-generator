<script setup lang="ts">
import { onClickOutside } from '@vueuse/core';
import { ref, computed } from 'vue';

const props = defineProps<{
    modelValue: string;
    options: {
        raw: string;
        formatted: string;
    }[];
    placeholder?: string;
    disabled?: boolean;
}>();

const emit = defineEmits(['update:modelValue']);

const containerRef = ref<HTMLElement | null>(null);

const isOpen = ref(false);
const search = ref('');

const filteredOptions = computed(() => {
    return props.options.filter((option) =>
        option.formatted.toLowerCase().includes(search.value.toLowerCase()),
    );
});

const selectOption = (option: { raw: string; formatted: string }) => {
    emit('update:modelValue', option.raw);
    isOpen.value = false;
    search.value = '';
};

const selectedLabel = computed(() => {
    const found = props.options.find((o) => o.raw === props.modelValue);

    return found?.formatted || '';
});

onClickOutside(containerRef, () => {
    isOpen.value = false;
});
</script>

<template>
    <div class="relative w-full" ref="containerRef">
        <button
            type="button"
            class="w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-left text-sm focus:ring-2 focus:ring-blue-500"
            @click="isOpen = !isOpen"
            :disabled="disabled"
        >
            {{ selectedLabel || placeholder || 'Select an option' }}
        </button>

        <div
            v-if="isOpen && !disabled"
            class="absolute z-10 mt-1 w-full rounded-md border border-gray-200 bg-white shadow-lg"
        >
            <input
                v-model="search"
                type="text"
                placeholder="Search..."
                class="w-full border-b border-gray-200 px-3 py-2 text-sm focus:outline-none"
            />

            <ul class="max-h-48 overflow-y-auto">
                <li
                    v-for="option in filteredOptions"
                    :key="option.raw"
                    @click="selectOption(option)"
                    class="cursor-pointer px-3 py-2 text-sm hover:bg-blue-100"
                >
                    {{ option.formatted }}
                </li>

                <li
                    v-if="filteredOptions.length === 0"
                    class="px-3 py-2 text-sm text-gray-500"
                >
                    No results found
                </li>
            </ul>
        </div>
    </div>
</template>
