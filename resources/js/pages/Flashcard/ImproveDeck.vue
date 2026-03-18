<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import Alert from '@/components/Alert.vue';
import InputError from '@/components/InputError.vue';
import InputLabel from '@/components/InputLabel.vue';
import Layout from '@/components/Layout.vue';
import PrimaryButton from '@/components/PrimaryButton.vue';
import SelectInput from '@/components/SelectInput.vue';

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

        <div class="max-w-xl mx-auto space-y-8 bg-white p-8 rounded-xl border border-gray-200 shadow-sm">
            <header class="text-center">
                <h1 class="text-2xl font-bold text-gray-900">
                    Otimizar Deck
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    A IA irá destacar palavras-chave e simplificar sentenças complexas.
                </p>
            </header>

            <Alert v-if="successMessage" type="success">
                {{ successMessage }}
            </Alert>

            <Alert v-if="errorMessage" type="error">
                <div class="flex items-center justify-between">
                    <span>{{ errorMessage }}</span>
                    <button
                        v-if="!loadingDecks && deckNames.length === 0"
                        @click="fetchDecks"
                        class="ml-4 underline hover:no-underline"
                    >
                        Tentar Novamente
                    </button>
                </div>
            </Alert>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-1.5">
                    <InputLabel for="deck_name" value="Selecione o Deck do Anki" />

                    <SelectInput
                        v-model="form.deck_name"
                        :options="deckNames"
                        :disabled="loadingDecks || deckNames.length === 0"
                        :placeholder="
                            loadingDecks
                                ? 'Carregando decks...'
                                : 'Escolha um deck para otimizar'
                        "
                    />

                    <p v-if="!loadingDecks && deckNames.length === 0" class="text-xs text-amber-600">
                        Nenhum deck encontrado. Abra o Anki com o plugin AnkiConnect.
                    </p>
                    <InputError :message="form.errors.deck_name" />
                </div>

                <div class="flex pt-4">
                    <PrimaryButton class="w-full justify-center" :disabled="form.processing || !form.deck_name">
                        {{ form.processing ? 'Otimizando...' : 'Otimizar Agora' }}
                    </PrimaryButton>
                </div>
            </form>
        </div>
    </Layout>
</template>
