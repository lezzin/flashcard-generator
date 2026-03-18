<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/components/PrimaryButton.vue';
</script>

<template>
    <Head title="Sucesso - Google Conectado" />

    <div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center px-4">
        <main class="max-w-md w-full text-center space-y-8 bg-white p-10 rounded-3xl shadow-md border border-gray-200">
            <div class="flex flex-col items-center">
                <div class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                
                <h1 class="text-3xl font-extrabold text-gray-900">
                    Google Conectado!
                </h1>

                <p class="mt-4 text-gray-600 text-lg">
                    Sua conta do Google foi vinculada com sucesso ao FlashIA. Agora você já pode exportar seus flashcards.
                </p>
            </div>

            <div class="pt-6">
                <Link href="/" class="block">
                    <PrimaryButton class="w-full justify-center py-4 text-lg font-semibold shadow-sm transition">
                        🏠 Voltar para o Início
                    </PrimaryButton>
                </Link>
            </div>
        </main>

        <footer class="mt-8 text-sm text-gray-400">
            © 2026 FlashIA • Conectado com segurança
        </footer>
    </div>
</template>
