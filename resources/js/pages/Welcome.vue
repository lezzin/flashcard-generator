<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/components/PrimaryButton.vue';
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
                <Link href="/flashcard/generate" class="w-full sm:w-auto">
                    <PrimaryButton
                        class="w-full sm:w-auto px-10 py-4 text-lg font-semibold justify-center shadow-sm hover:scale-[1.02] transition">
                        🚀 Gerar Flashcards
                    </PrimaryButton>
                </Link>

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