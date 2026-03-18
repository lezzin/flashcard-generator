<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Layout from '@/components/Layout.vue';
import Button from '@/components/ui/button.vue';
import Input from '@/components/ui/input.vue';
import Textarea from '@/components/ui/textarea.vue';
import Label from '@/components/ui/label.vue';
import Alert from '@/components/ui/alert.vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';

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
        const response = await fetch('/api/flashcards', {
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
                        <Input
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="ex: Biologia - Células"
                            autofocus
                            :class="{ 'border-red-500': form.errors.title }"
                        />
                        <p
                            v-if="form.errors.title"
                            class="text-sm font-medium text-red-500"
                        >
                            {{ form.errors.title }}
                        </p>
                    </div>

                    <div class="space-y-2">
                        <Label for="content">Conteúdo para Estudo</Label>
                        <Textarea
                            id="content"
                            v-model="form.content"
                            rows="8"
                            placeholder="Cole o texto aqui..."
                            :class="{ 'border-red-500': form.errors.content }"
                        />
                        <p
                            v-if="form.errors.content"
                            class="text-sm font-medium text-red-500"
                        >
                            {{ form.errors.content }}
                        </p>
                    </div>

                    <div class="pt-4">
                        <Button
                            class="w-full"
                            :disabled="
                                form.processing || !form.title || !form.content
                            "
                        >
                            {{
                                form.processing
                                    ? 'Processando...'
                                    : 'Gerar Agora'
                            }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </Layout>
</template>
