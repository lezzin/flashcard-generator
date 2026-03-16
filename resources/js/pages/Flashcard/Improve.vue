<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { onMounted, ref } from 'vue'
import Layout from '@/components/Layout.vue'
import Alert from '@/components/Alert.vue'
import InputLabel from '@/components/InputLabel.vue'
import SelectInput from '@/components/SelectInput.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/PrimaryButton.vue'

const successMessage = ref<string | null>(null)
const errorMessage = ref<string | null>(null)
const deckNames = ref<string[]>([])
const loadingDecks = ref(true)

const form = useForm({
    deck_name: '',
})

const fetchDecks = async () => {
    loadingDecks.value = true
    errorMessage.value = null
    try {
        const response = await fetch('/api/flashcard/deck', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            }
        })

        if (!response.ok) {
            throw new Error('Failed to load decks.')
        }

        deckNames.value = await response.json()
    } catch (error) {
        errorMessage.value = 'Failed to load deck list. Please ensure Anki is running with AnkiConnect.'
    } finally {
        loadingDecks.value = false
    }
}

const submit = async () => {
    successMessage.value = null
    errorMessage.value = null
    form.clearErrors()
    form.processing = true

    try {
        const response = await fetch('/api/flashcard/improve', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                deck_name: form.deck_name,
            }),
        })

        const result = await response.json()

        if (!response.ok) {
            if (response.status === 422 && result.errors) {
                Object.keys(result.errors).forEach((key) => {
                    form.setError(key as any, result.errors[key][0])
                })
            } else {
                errorMessage.value = result.message || 'An error occurred while attempting to improve cards.'
            }
            return
        }

        successMessage.value = 'Successfully queued. Your flashcards are being optimized!'
        form.reset()
    } catch (error) {
        errorMessage.value = 'An unexpected error occurred. Please check your connection and try again.'
    } finally {
        form.processing = false
    }
}

onMounted(fetchDecks)
</script>

<template>
    <Layout>
        <Head title="Improve Flashcards" />

        <div class="mx-auto max-w-2xl px-4">
            <header class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Optimize Existing Deck</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Enhance your flashcards by identifying and bolding critical keywords.
                </p>
            </header>

            <div class="space-y-6">
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
                            Retry
                        </button>
                    </div>
                </Alert>

                <form @submit.prevent="submit" class="space-y-6 rounded-xl bg-white p-8 shadow-sm border border-gray-100">
                    <div class="space-y-1.5">
                        <InputLabel for="deck_name" value="Select Anki Deck" />
                        <SelectInput
                            id="deck_name"
                            v-model="form.deck_name"
                            :disabled="loadingDecks || deckNames.length === 0"
                        >
                            <option value="" disabled>
                                {{ loadingDecks ? 'Loading decks...' : 'Choose a deck to optimize' }}
                            </option>
                            <option v-for="name in deckNames" :key="name" :value="name">
                                {{ name }}
                            </option>
                        </SelectInput>
                        <p v-if="!loadingDecks && deckNames.length === 0" class="text-xs text-amber-600">
                            No decks found. Make sure Anki is open with the AnkiConnect plugin installed.
                        </p>
                        <InputError :message="form.errors.deck_name" />
                    </div>

                    <div class="flex items-center justify-end border-t border-gray-100 pt-6">
                        <PrimaryButton :disabled="form.processing || !form.deck_name">
                            {{ form.processing ? 'Optimizing...' : 'Improve Deck' }}
                        </PrimaryButton>
                    </div>
                </form>

                <div class="rounded-lg border border-blue-100 bg-blue-50 p-6">
                    <h3 class="text-sm font-semibold text-blue-900 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-blue-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                        What happens next?
                    </h3>
                    <ul class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 text-xs text-blue-800 leading-relaxed">
                        <li>The system will fetch all cards from your selected deck.</li>
                        <li>It will analyze and <strong>bold</strong> critical keywords.</li>
                        <li>Complex sentences will be simplified for better recall.</li>
                        <li>Changes are automatically updated in your local Anki.</li>
                    </ul>
                </div>
            </div>
        </div>
    </Layout>
</template>
