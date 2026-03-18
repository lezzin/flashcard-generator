<script setup lang="ts">
import { computed } from 'vue';
import FolderIcon from './FolderIcon.vue';
import FileIcon from './FileIcon.vue';

const props = defineProps<{
    node: any;
}>();

const emit = defineEmits<{
    (e: 'enterFolder', node: any): void;
}>();

const isFolder = computed(() => props.node.type === 'folder');

const handleClick = () => {
    if (isFolder.value) {
        emit('enterFolder', props.node);
        return;
    }

    window.open(props.node.downloadUrl, '_blank');
};
</script>

<template>
    <div
        class="flex cursor-pointer items-center gap-2 border border-gray-200 p-2 px-4 hover:bg-gray-100"
        @click="handleClick"
    >
        <span class="text-blue-500">
            <FolderIcon v-if="isFolder" />
            <FileIcon class="text-gray-500" v-else />
        </span>

        <span class="truncate">{{ node.name }}</span>
    </div>
</template>
