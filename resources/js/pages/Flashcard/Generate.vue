<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Layout from '@/components/Layout.vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import Alert from '@/components/ui/alert.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import Input from '@/components/ui/input.vue';
import Textarea from '@/components/ui/textarea.vue';
import Button from '@/components/ui/button.vue';
import Label from '@/components/ui/label.vue';

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const form = useForm({
    title: '',
    content: '',
    file: ''
});

const activeTab = ref<'text' | 'file'>('text');
const file = ref<File | null>(null);
const isDragging = ref(false);

const handleFileChange = (e: Event) => {
    const target = e.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        file.value = target.files[0];
    }
};

const handleDrop = (e: DragEvent) => {
    isDragging.value = false;
    const dropped = e.dataTransfer?.files[0];
    if (dropped) file.value = dropped;
};

const submit = async () => {
    successMessage.value = null;
    errorMessage.value = null;
    form.clearErrors();
    form.processing = true;

    try {
        let response;

        if (activeTab.value === 'file') {
            const data = new FormData();

            data.append('title', form.title);
            if (file.value) data.append('content', file.value);

            response = await fetch('/api/flashcards', {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: data
            });
        } else {
            response = await fetch('/api/flashcards', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ title: form.title, content: form.content }),
            });
        }

        const result = response.status !== 204 ? await response.json() : null;

        if (!response.ok) {
            if (response.status === 422 && result?.errors) {
                Object.keys(result.errors).forEach((key) => {
                    let formKey = key;
                    if (activeTab.value === 'file' && key === 'content') {
                        formKey = 'file';
                    }
                    form.setError(formKey as any, result.errors[key][0]);
                });
            } else {
                errorMessage.value = result?.message || 'Ocorreu um erro ao processar sua solicitação.';
            }

            return;
        }

        successMessage.value = 'Sucesso! Seus flashcards estão sendo gerados em segundo plano.';
        form.reset();
        file.value = null;
    } catch (_) {
        console.error(_);
        errorMessage.value = 'Ocorreu um erro de rede ou inesperado. Tente novamente.';
    } finally {
        form.processing = false;
    }
};

const isButtonDisabled = computed(() => (
    form.processing ||
    !form.title ||
    (activeTab.value === 'text' ? !form.content : !file.value)
));

const getButtonVariant = (target: 'file' | 'text') => (
    activeTab.value === target ? 'default' : 'outline'
)
</script>

<template>
    <Layout>

        <Head title="Gerar Flashcards" />

        <Card class="mx-auto max-w-xl border-gray-200 shadow-sm">
            <CardHeader class="text-center">
                <CardTitle>Gerar Flashcards</CardTitle>
                <CardDescription>
                    Cole o conteúdo e a IA fará o resto.
                </CardDescription>
            </CardHeader>

            <CardContent class="space-y-6">
                <Alert v-if="successMessage" variant="success">
                    {{ successMessage }}
                </Alert>

                <Alert v-if="errorMessage" variant="destructive">
                    {{ errorMessage }}
                </Alert>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="title">Nome do Deck</Label>
                        <Input id="title" v-model="form.title" type="text" placeholder="ex: Biologia - Células"
                            autofocus :class="{ 'border-red-500': form.errors.title }" />
                        <p v-if="form.errors.title" class="text-sm font-medium text-red-500">
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <span class="block text-sm font-semibold text-gray-700">Conteúdo para Estudo</span>

                        <div class="grid md:grid-cols-2 rounded-xl bg-gray-100 p-1 gap-1">
                            <Button type="button" :variant="getButtonVariant('text')" @click="activeTab = 'text'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25H12" />
                                </svg>
                                Texto
                            </Button>

                            <Button type="button" :variant="getButtonVariant('file')" @click="activeTab = 'file'">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                </svg>
                                Arquivo
                            </Button>
                        </div>

                        <div v-if="activeTab === 'text'">
                            <Textarea id="content" v-model="form.content" rows="8" placeholder="Cole o texto aqui…"
                                :class="form.errors.content ? 'border-red-400 bg-red-50' : 'border-gray-300 bg-white'"></textarea>
                        </div>

                        <div v-else
                            class="relative flex flex-col items-center justify-center gap-3 rounded-xl border-2 border-dashed px-6 py-10 text-center cursor-pointer transition-colors duration-150"
                            :class="[
                                isDragging
                                    ? 'border-indigo-400 bg-indigo-50'
                                    : 'border-gray-300 bg-gray-50 hover:border-indigo-400 hover:bg-indigo-50',
                                form.errors.file ? 'border-red-400 bg-red-50' : ''
                            ]" @dragover.prevent="isDragging = true" @dragleave="isDragging = false"
                            @drop.prevent="handleDrop">

                            <input type="file" accept=".json"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                                @change="handleFileChange" />

                            <div class="flex items-center justify-center w-12 h-12 rounded-xl bg-indigo-100">
                                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor"
                                    stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">
                                    {{ file ? file.name : 'Arraste ou clique para enviar' }}
                                </p>
                                <p class="mt-0.5 text-xs text-gray-400">Apenas arquivos JSON</p>
                            </div>
                        </div>

                        <p v-if="form.errors.content" class="text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ form.errors.content }}
                        </p>
                        <p v-if="form.errors.file" class="text-xs text-red-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                            {{ form.errors.file }}
                        </p>
                    </div>

                    <div class="pt-4">
                        <Button class="w-full" :disabled="isButtonDisabled">
                            {{ form.processing ? 'Processando...' : 'Gerar Agora' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </Layout>
</template>
