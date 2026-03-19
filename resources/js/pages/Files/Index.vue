<script setup lang="ts">
import Breadcrumbs from '@/components/file-explorer/Breadcrumbs.vue';
import Layout from '@/components/Layout.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Alert from '@/components/ui/alert.vue';
import { Head } from '@inertiajs/vue3';
import { computed, onMounted, ref } from 'vue';
import FolderIcon from '@/components/file-explorer/FolderIcon.vue';
import FileIcon from '@/components/file-explorer/FileIcon.vue';

type FileNode = {
    id: string | number;
    name: string;
    type: 'file' | 'folder';
    createdTime?: string;
    downloadUrl?: string;
    children?: FileNode[];
};

const tree = ref<FileNode | null>(null);
const loading = ref(true);
const errorMessage = ref<string | null>(null);
const currentPath = ref<FileNode[]>([]);

const currentNode = computed(() => currentPath.value.at(-1) ?? null);

const currentPathChildren = computed<FileNode[]>(() => {
    return currentNode.value?.children ?? [];
});

const isFolder = (node: FileNode) => node.type === 'folder';

const fetchTree = async () => {
    loading.value = true;
    errorMessage.value = null;

    try {
        const res = await fetch('/api/files');

        if (!res.ok) {
            throw new Error();
        }

        const data: FileNode = await res.json();

        tree.value = data;
        currentPath.value = [data];
    } catch {
        errorMessage.value = 'Falha ao carregar arquivos.';
    } finally {
        loading.value = false;
    }
};

const handleClick = (node: FileNode) => {
    if (isFolder(node)) {
        currentPath.value.push(node);
        return;
    }

    if (node.downloadUrl) {
        window.open(node.downloadUrl, '_blank');
    }
};

const goToPath = (index: number) => {
    currentPath.value = currentPath.value.slice(0, index + 1);
};

onMounted(fetchTree);
</script>

<template>
    <Layout>

        <Head title="Arquivos" />

        <Card class="mx-auto max-w-5xl border-gray-200 shadow-sm">
            <CardContent class="space-y-4 p-6 md:p-8">
                <Breadcrumbs v-if="currentPath.length" :path="currentPath" @navigate="goToPath" />

                <div v-if="loading" class="py-8 text-center text-gray-500">
                    Carregando arquivos...
                </div>

                <Alert v-else-if="errorMessage" variant="destructive">
                    {{ errorMessage }}
                </Alert>

                <div v-else>
                    <table class="w-full text-left border-b border-gray-200">
                        <thead>
                            <tr>
                                <th class="p-2 px-4 text-sm font-medium text-gray-600 cursor-default">
                                    Nome
                                </th>
                                <th class="p-2 px-4 text-sm font-medium text-gray-600 cursor-default">
                                    Data de criação
                                </th>
                            </tr>
                        </thead>

                        <tbody>
                            <tr v-for="child in currentPathChildren" :key="child.id"
                                class="cursor-pointer border-t border-gray-200 hover:bg-gray-50"
                                @click="handleClick(child)">
                                <td class="p-2 px-4">
                                    <div class="flex items-center gap-2">
                                        <span>
                                            <FolderIcon v-if="isFolder(child)" class="text-blue-500" />
                                            <FileIcon v-else class="text-gray-400" />
                                        </span>

                                        <span class="truncate">
                                            {{ child.name }}
                                        </span>
                                    </div>
                                </td>

                                <td class="p-2 px-4">
                                    <span class="text-xs text-gray-500 tabular-nums">
                                        {{ child.createdTime ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="!currentPathChildren.length" class="p-8 text-center text-gray-400 italic">
                        Esta pasta está vazia.
                    </div>
                </div>
            </CardContent>
        </Card>
    </Layout>
</template>