<script setup lang="ts">
import Breadcrumbs from '@/components/File/Breadcrumbs.vue';
import TreeNode from '@/components/File/TreeNode.vue';
import Layout from '@/components/Layout.vue';
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';

const tree = ref(null);
const loading = ref(true);
const errorMessage = ref<string | null>(null);
const currentPath = ref<any[]>([]);

const fetchTree = async () => {
    loading.value = true;
    errorMessage.value = null;

    try {
        const res = await fetch('/api/files');

        if (!res.ok) {
            throw new Error('Erro ao carregar arquivos.');
        }

        tree.value = await res.json();
        currentPath.value = [tree.value];
    } catch (e) {
        errorMessage.value = 'Falha ao carregar arquivos.';
    } finally {
        loading.value = false;
    }
};

const enterFolder = (node: any) => {
    if (node.type === 'folder') currentPath.value.push(node);
};

const goToPath = (index: number) => {
    currentPath.value = currentPath.value.slice(0, index + 1);
};

onMounted(fetchTree);
</script>

<template>
    <Layout>

        <Head title="Arquivos" />

        <div class="max-w-5xl mx-auto space-y-8 bg-white p-8 rounded-xl border border-gray-200 shadow-sm">
            <Breadcrumbs :path="currentPath" @navigate="goToPath" />

            <div v-if="loading" class="text-center">Carregando...</div>
            <div v-else-if="errorMessage">{{ errorMessage }}</div>

            <div v-else>
                <TreeNode v-for="child in currentPath[currentPath.length - 1].children" :key="child.id" :node="child"
                    @enterFolder="enterFolder" />
            </div>
        </div>
    </Layout>
</template>