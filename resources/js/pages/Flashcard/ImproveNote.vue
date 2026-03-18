<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Layout from '@/components/Layout.vue';
import Button from '@/components/ui/button.vue';
import Input from '@/components/ui/input.vue';
import Label from '@/components/ui/label.vue';
import Alert from '@/components/ui/alert.vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';

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

        <Card class="max-w-xl mx-auto border-gray-200 shadow-sm">
            <CardHeader class="text-center">
                <CardTitle>Otimizar Nota Única</CardTitle>
                <CardDescription>
                    Insira o ID da nota do Anki para otimização individual.
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
                        <Label for="note_id">ID da Nota</Label>
                        <Input
                            id="note_id"
                            v-model="form.note_id"
                            placeholder="ex: 123456789"
                            :class="{'border-red-500': form.errors.note_id}"
                        />
                        <p v-if="form.errors.note_id" class="text-sm text-red-500 font-medium">
                            {{ form.errors.note_id }}
                        </p>
                    </div>

                    <div class="pt-4">
                        <Button class="w-full" :disabled="form.processing || !form.note_id">
                            {{ form.processing ? 'Processando...' : 'Otimizar Nota' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </Layout>
</template>
