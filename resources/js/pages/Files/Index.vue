<script setup lang="ts">
import Breadcrumbs from '@/components/file-explorer/Breadcrumbs.vue';
import TreeNode from '@/components/file-explorer/TreeNode.vue';
import Layout from '@/components/Layout.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Alert from '@/components/ui/alert.vue';
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

        <Card class="max-w-5xl mx-auto border-gray-200 shadow-sm">
            <CardContent class="p-8 space-y-4">
                <Breadcrumbs v-if="currentPath.length > 0" :path="currentPath" @navigate="goToPath" />

                <div v-if="loading" class="text-center py-8 text-gray-500">
                    Carregando arquivos...
                </div>

                <Alert v-else-if="errorMessage" variant="destructive">
                    {{ errorMessage }}
                </Alert>

                <div v-else>
                    <TreeNode v-for="child in currentPath[currentPath.length - 1].children" :key="child.id"
                        :node="child" @enterFolder="enterFolder" class="first:rounded-t-md last:rounded-b-md" />

                    <div v-if="currentPath[currentPath.length - 1].children.length === 0"
                        class="p-8 text-center text-gray-400 italic">
                        Esta pasta está vazia.
                    </div>
                </div>
            </CardContent>
        </Card>
    </Layout>
</template>