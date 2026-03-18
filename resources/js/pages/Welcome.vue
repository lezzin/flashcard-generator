<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import PrimaryButton from '@/components/PrimaryButton.vue';

const isConnecting = ref(false);

const connectGoogle = async () => {
    isConnecting.value = true;
    try {
        const response = await fetch('/api/google/auth');
        const data = await response.json();

        if (data.url) {
            window.location.href = data.url;
        }
    } catch (error) {
        console.error('Erro ao buscar URL de autenticação:', error);
    } finally {
        isConnecting.value = false;
    }
};
</script>

<template>

    <Head title="FlashIA - Estude mais rápido com IA" />

    <div class="min-h-screen bg-linear-to-b from-gray-50 to-gray-100 flex flex-col items-center justify-center px-4">
        <main
            class="max-w-3xl w-full text-center space-y-10 bg-white p-10 sm:p-14 rounded-3xl shadow-md border border-gray-200">

            <header class="space-y-4">
                <h1 class="text-5xl sm:text-6xl font-extrabold tracking-tight text-gray-900">
                    Flash<span class="text-blue-600">IA</span>
                </h1>

                <p class="text-xl text-gray-600 max-w-xl mx-auto">
                    Transforme qualquer conteúdo em flashcards inteligentes e acelere seu aprendizado com IA.
                </p>
            </header>

            <div class="flex flex-col items-center gap-5">
                <div class="flex flex-col sm:flex-row gap-4 w-full justify-center">
                    <Link href="/flashcard/generate" class="w-full sm:w-auto">
                        <PrimaryButton
                            class="w-full sm:w-auto px-10 py-4 text-lg font-semibold justify-center shadow-sm hover:scale-[1.02] transition">
                            🚀 Gerar Flashcards
                        </PrimaryButton>
                    </Link>

                    <PrimaryButton @click="connectGoogle" :disabled="isConnecting"
                        class="w-full sm:w-auto px-10 py-4 text-lg font-semibold justify-center bg-white! text-gray-700! border border-gray-300 hover:bg-gray-50 hover:scale-[1.02] transition shadow-sm">
                        <template v-if="isConnecting">
                            Conectando...
                        </template>
                        <template v-else>
                            <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                                <path fill="currentColor"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                                <path fill="currentColor"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                                <path fill="currentColor"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                                <path fill="currentColor"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                            </svg>
                            Conectar Google
                        </template>
                    </PrimaryButton>
                </div>

                <div class="flex flex-wrap justify-center gap-6 pt-2">
                    <Link href="/deck/improve"
                        class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">
                        ✨ Melhorar Decks
                    </Link>

                    <Link href="/note/improve"
                        class="text-sm font-semibold text-gray-600 hover:text-blue-600 transition">
                        📝 Melhorar Nota
                    </Link>
                </div>
            </div>

            <div class="pt-10 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-3 gap-8 text-left">
                <div class="space-y-2">
                    <h3 class="font-bold text-gray-900">⚡ Rápido</h3>
                    <p class="text-sm text-gray-600">
                        Gere decks completos em segundos, sem esforço manual.
                    </p>
                </div>

                <div class="space-y-2">
                    <h3 class="font-bold text-gray-900">🧠 Inteligente</h3>
                    <p class="text-sm text-gray-600">
                        IA identifica conceitos importantes automaticamente.
                    </p>
                </div>

                <div class="space-y-2">
                    <h3 class="font-bold text-gray-900">🔄 Integrado</h3>
                    <p class="text-sm text-gray-600">
                        Envie direto para o Anki sem complicação.
                    </p>
                </div>
            </div>
        </main>

        <footer class="mt-8 text-sm text-gray-400">
            © 2026 FlashIA • Estude melhor, não mais difícil
        </footer>
    </div>
</template>