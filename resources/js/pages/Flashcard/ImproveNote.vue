<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Alert from '@/components/Alert.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Layout from '@/components/Layout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import TextInput from '@/components/TextInput.vue';

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);

const form = useForm({
    note_id: '',
});

const submit = async () => {
    successMessage.value = null;
    errorMessage.value = null;
    form.clearErrors();
    form.processing = true;

    try {
        const response = await fetch('/api/notes/improve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                note_id: form.note_id,
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
                    'Ocorreu um erro ao tentar otimizar a nota.';
            }

            return;
        }

        successMessage.value = 'Sua nota foi otimizada com sucesso!';
        form.reset();
    } catch (_) {
        errorMessage.value =
            'Ocorreu um erro inesperado. Verifique sua conexão e tente novamente.';
    } finally {
        form.processing = false;
    }
};
</script>

<template>
    <Layout>
        <Head title="Otimizar Nota" />

        <div class="max-w-xl mx-auto space-y-8 bg-white p-8 rounded-xl border border-gray-200 shadow-sm">
            <header class="text-center">
                <h1 class="text-2xl font-bold text-gray-900">
                    Otimizar Nota Única
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    Insira o ID da nota do Anki para otimização individual.
                </p>
            </header>

            <Alert v-if="successMessage" type="success">
                {{ successMessage }}
            </Alert>

            <Alert v-if="errorMessage" type="error">
                {{ errorMessage }}
            </Alert>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-1.5">
                    <InputLabel for="note_id" value="ID da Nota" />
                    <TextInput id="note_id" v-model="form.note_id" placeholder="ex: 123456789" />
                    <InputError :message="form.errors.note_id" />
                </div>

                <div class="flex pt-4">
                    <PrimaryButton class="w-full justify-center" :disabled="form.processing || !form.note_id">
                        {{ form.processing ? 'Processando...' : 'Otimizar Nota' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Layout>
</template>
