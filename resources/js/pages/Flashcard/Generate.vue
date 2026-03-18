<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Alert from '@/components/Alert.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Layout from '@/components/Layout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextArea from '@/components/TextArea.vue';
import TextInput from '@/components/TextInput.vue';

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const form = useForm({
    title: '',
    content: '',
});

const submit = async () => {
    successMessage.value = null;
    errorMessage.value = null;
    form.clearErrors();
    form.processing = true;

    try {
        const response = await fetch('/api/flashcard/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                title: form.title,
                content: form.content,
            }),
        });

        const result = await response.json();

        if (!response.ok) {
            if (response.status === 422 && result.errors) {
                Object.keys(result.errors).forEach((key) => {
                    form.setError(key as any, result.errors[key][0]);
                });
            } else {
                errorMessage.value =
                    result.message ||
                    'Ocorreu um erro ao processar sua solicitação.';
            }

            return;
        }

        successMessage.value =
            'Sucesso! Seus flashcards estão sendo gerados em segundo plano.';
        form.reset();
    } catch (_) {
        errorMessage.value =
            'Ocorreu um erro de rede ou inesperado. Tente novamente.';
    } finally {
        form.processing = false;
    }
};
</script>

<template>
    <Layout>

        <Head title="Gerar Flashcards" />

        <div class="mx-auto max-w-2xl px-4">
            <header class="mb-10 text-center">
                <h1 class="text-3xl font-bold text-gray-900">
                    Criar Novo Deck
                </h1>
                <p class="mt-2 text-sm text-gray-600">
                    Cole seu conteúdo de estudo e deixe a IA criar os flashcards perfeitos.
                </p>
            </header>

            <div class="space-y-6">
                <Alert v-if="successMessage" type="success">
                    {{ successMessage }}
                </Alert>

                <Alert v-if="errorMessage" type="error">
                    {{ errorMessage }}
                </Alert>

                <form @submit.prevent="submit"
                    class="space-y-6 rounded-xl border border-gray-100 bg-white p-8 shadow-sm">
                    <div class="space-y-1.5">
                        <InputLabel for="title" value="Título do Deck" />
                        <TextInput id="title" v-model="form.title" type="text"
                            placeholder="ex: Biologia - Respiração Celular" autofocus />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="content" value="Conteúdo de Estudo" />
                        <TextArea id="content" v-model="form.content" rows="10"
                            placeholder="Cole o texto que você quer aprender..." />
                        <p class="text-xs text-gray-500">
                            Forneça uma descrição clara para melhores resultados.
                        </p>
                        <InputError :message="form.errors.content" />
                    </div>

                    <div class="flex items-center justify-end border-t border-gray-100 pt-6">
                        <PrimaryButton :disabled="form.processing || !form.title || !form.content
                            ">
                            {{
                                form.processing
                                    ? 'Gerando...'
                                    : 'Iniciar Geração'
                            }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>