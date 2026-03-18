<script setup lang="ts">
const props = defineProps<{ path: any[] }>();
const emit = defineEmits<{ (e: 'navigate', index: number): void }>();
</script>

<template>
    <div class="flex items-center gap-1 text-sm mb-4">
        <span v-for="(node, i) in path" :key="i" class="flex items-center">
            <button v-if="i < path.length - 1"
                class="text-xl font-semibold py-2 px-4 hover:bg-gray-200 rounded-full text-blue-600 cursor-pointer"
                @click="$emit('navigate', i)">
                {{ node.name }}
            </button>

            <span v-else class="text-xl font-semibold py-2 px-4 rounded-full">{{ node.name }}</span>

            <span v-if="i < path.length - 1">
                <svg class=" a-s-fa-Ha-pa c-qd" width="24px" height="24px" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"></path>
                </svg>
            </span>
        </span>
    </div>
</template>