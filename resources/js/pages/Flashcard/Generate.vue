<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3'
import { ref } from 'vue'
import Layout from '@/components/Layout.vue'
import Alert from '@/components/Alert.vue'
import InputLabel from '@/components/InputLabel.vue'
import TextInput from '@/components/TextInput.vue'
import TextArea from '@/components/TextArea.vue'
import InputError from '@/components/InputError.vue'
import PrimaryButton from '@/components/PrimaryButton.vue'

const successMessage = ref<string | null>(null)
const errorMessage = ref<string | null>(null)

const form = useForm({
    title: '',
    content: '',
})

const submit = async () => {
    successMessage.value = null
    errorMessage.value = null
    form.clearErrors()
    form.processing = true

    try {
        const response = await fetch('/api/flashcard/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                title: form.title,
                content: form.content,
            }),
        })

        const result = await response.json()

        if (!response.ok) {
            if (response.status === 422 && result.errors) {
                Object.keys(result.errors).forEach((key) => {
                    form.setError(key as any, result.errors[key][0])
                })
            } else {
                errorMessage.value = result.message || 'An error occurred while processing your request.'
            }
            return
        }

        successMessage.value = 'Success! Your flashcards are being generated in the background.'
        form.reset()
    } catch (error) {
        errorMessage.value = 'A network error or unexpected issue occurred. Please try again.'
    } finally {
        form.processing = false
    }
}
</script>

<template>
    <Layout>
        <Head title="Generate Flashcards" />

        <div class="mx-auto max-w-2xl px-4">
            <header class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-900">Create New Deck</h1>
                <p class="mt-2 text-sm text-gray-600">
                    Paste your study content and let AI create the perfect flashcards.
                </p>
            </header>

            <div class="space-y-6">
                <Alert v-if="successMessage" type="success">
                    {{ successMessage }}
                </Alert>

                <Alert v-if="errorMessage" type="error">
                    {{ errorMessage }}
                </Alert>

                <form @submit.prevent="submit" class="space-y-6 rounded-xl bg-white p-8 shadow-sm border border-gray-100">
                    <div class="space-y-1.5">
                        <InputLabel for="title" value="Deck Title" />
                        <TextInput
                            id="title"
                            v-model="form.title"
                            type="text"
                            placeholder="e.g., Biology - Cellular Respiration"
                            autofocus
                        />
                        <InputError :message="form.errors.title" />
                    </div>

                    <div class="space-y-1.5">
                        <InputLabel for="content" value="Study Content" />
                        <TextArea
                            id="content"
                            v-model="form.content"
                            rows="10"
                            placeholder="Paste the text you want to learn from..."
                        />
                        <p class="text-xs text-gray-500">Provide a clear description for better results.</p>
                        <InputError :message="form.errors.content" />
                    </div>

                    <div class="flex items-center justify-end border-t border-gray-100 pt-6">
                        <PrimaryButton :disabled="form.processing || !form.title || !form.content">
                            {{ form.processing ? 'Generating...' : 'Start Generating' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </Layout>
</template>
