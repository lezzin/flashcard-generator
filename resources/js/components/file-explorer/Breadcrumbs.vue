<script setup lang="ts">
const props = defineProps<{ path: any[] }>();
const emit = defineEmits<{ (e: 'navigate', index: number): void }>();
</script>

<template>
    <div class="mb-4 flex items-center gap-1 text-sm">
        <span
            v-for="(node, i) in path"
            :key="i"
            class="flex items-center gap-1"
        >
            <button
                v-if="i < path.length - 1"
                class="cursor-pointer rounded-full px-3 py-1 text-xl font-semibold text-blue-600 hover:bg-gray-200"
                @click="$emit('navigate', i)"
            >
                {{ node.name }}
            </button>

            <span v-else class="px-3 py-1 text-xl font-semibold">{{
                node.name
            }}</span>

            <span v-if="i < path.length - 1">
                <svg
                    class="a-s-fa-Ha-pa c-qd"
                    width="24px"
                    height="24px"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                >
                    <path
                        d="M10 6L8.59 7.41 13.17 12l-4.58 4.59L10 18l6-6z"
                    ></path>
                </svg>
            </span>
        </span>
    </div>
</template>
