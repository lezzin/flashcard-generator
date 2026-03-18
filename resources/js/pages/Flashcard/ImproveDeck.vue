<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Layout from '@/components/Layout.vue';
import Button from '@/components/ui/button.vue';
import Label from '@/components/ui/label.vue';
import Alert from '@/components/ui/alert.vue';
import SelectSearch from '@/components/ui/select-search.vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';

type DeckName = {
    raw: string;
    formatted: string;
};

const successMessage = ref<string | null>(null);
const errorMessage = ref<string | null>(null);
const deckNames = ref<DeckName[]>([]);
const loadingDecks = ref(true);

const form = useForm({
    deck_name: '',
});

const fetchDecks = async () => {
    loadingDecks.value = true;
    errorMessage.value = null;

    try {
        const response = await fetch('/api/decks', {
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            throw new Error('Falha ao carregar decks.');
        }

        deckNames.value = await response.json();
    } catch (_) {
        errorMessage.value =
            'Falha ao carregar lista de decks. Certifique-se que o Anki está aberto com o AnkiConnect instalado.';
    } finally {
        loadingDecks.value = false;
    }
};

const submit = async () => {
    successMessage.value = null;
    errorMessage.value = null;
    form.clearErrors();
    form.processing = true;

    try {
        const response = await fetch('/api/decks/improve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                deck_name: form.deck_name,
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
                    'Ocorreu um erro ao tentar otimizar os cards.';
            }

            return;
        }

        successMessage.value = 'Seus flashcards foram otimizados com sucesso!';
        form.reset();
    } catch (_) {
        errorMessage.value =
            'Ocorreu um erro inesperado. Verifique sua conexão e tente novamente.';
    } finally {
        form.processing = false;
    }
};

onMounted(fetchDecks);
</script>

<template>
    <Layout>
        <Head title="Otimizar Deck" />

        <Card class="max-w-xl mx-auto border-gray-200 shadow-sm">
            <CardHeader class="text-center">
                <CardTitle>Otimizar Deck</CardTitle>
                <CardDescription>
                    A IA irá destacar palavras-chave e simplificar sentenças complexas.
                </CardDescription>
            </CardHeader>

            <CardContent class="space-y-6">
                <Alert v-if="successMessage" variant="success">
                    {{ successMessage }}
                </Alert>

                <Alert v-if="errorMessage" variant="destructive">
                    <div class="flex items-center justify-between w-full">
                        <span>{{ errorMessage }}</span>
                        <button
                            v-if="!loadingDecks && deckNames.length === 0"
                            @click="fetchDecks"
                            class="ml-4 underline hover:no-underline font-medium text-white"
                        >
                            Tentar Novamente
                        </button>
                    </div>
                </Alert>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="space-y-2">
                        <Label for="deck_name">Selecione o Deck do Anki</Label>

                        <SelectSearch
                            v-model="form.deck_name"
                            :options="deckNames"
                            :disabled="loadingDecks || deckNames.length === 0"
                            :placeholder="
                                loadingDecks
                                    ? 'Carregando decks...'
                                    : 'Escolha um deck para otimizar'
                            "
                            :class="{'border-red-500 ring-red-500': form.errors.deck_name}"
                        />

                        <p v-if="!loadingDecks && deckNames.length === 0" class="text-xs text-amber-600 font-medium">
                            Nenhum deck encontrado. Abra o Anki com o plugin AnkiConnect.
                        </p>
                        <p v-if="form.errors.deck_name" class="text-sm text-red-500 font-medium">
                            {{ form.errors.deck_name }}
                        </p>
                    </div>

                    <div class="pt-4">
                        <Button class="w-full" :disabled="form.processing || !form.deck_name">
                            {{ form.processing ? 'Otimizando...' : 'Otimizar Agora' }}
                        </Button>
                    </div>
                </form>
            </CardContent>
        </Card>
    </Layout>
</template>
