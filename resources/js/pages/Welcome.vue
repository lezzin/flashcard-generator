<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Button from '@/components/ui/button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';

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

    <div
        class="flex min-h-screen flex-col items-center justify-center bg-linear-to-b from-gray-50 to-gray-100 px-4 py-12"
    >
        <Card class="w-full max-w-3xl border-gray-200 shadow-xl">
            <CardHeader class="space-y-4 pt-10 pb-8 text-center sm:pt-14">
                <CardTitle
                    class="text-5xl font-extrabold tracking-tight text-gray-900 sm:text-6xl"
                >
                    Flash<span class="text-blue-600">IA</span>
                </CardTitle>
                <CardDescription class="mx-auto max-w-xl text-xl text-gray-600">
                    Transforme qualquer conteúdo em flashcards inteligentes e
                    acelere seu aprendizado com IA.
                </CardDescription>
            </CardHeader>

            <CardContent
                class="flex flex-col items-center gap-8 px-8 pb-10 sm:px-14"
            >
                <div
                    class="flex w-full flex-col justify-center gap-4 sm:flex-row"
                >
                    <Link href="/flashcard/generate" class="w-full sm:w-auto">
                        <Button
                            size="lg"
                            class="w-full px-8 py-6 text-lg shadow-md transition-all hover:scale-[1.02] hover:shadow-lg sm:w-auto"
                        >
                            🚀 Gerar Flashcards
                        </Button>
                    </Link>

                    <Button
                        variant="outline"
                        size="lg"
                        @click="connectGoogle"
                        :disabled="isConnecting"
                        class="w-full border-gray-300 px-8 py-6 text-lg shadow-sm transition-all hover:scale-[1.02] hover:bg-gray-50 hover:text-gray-900 sm:w-auto"
                    >
                        <template v-if="isConnecting"> Conectando... </template>
                        <template v-else>
                            <svg class="mr-2 h-5 w-5" viewBox="0 0 24 24">
                                <path
                                    fill="currentColor"
                                    d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                                />
                                <path
                                    fill="currentColor"
                                    d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                                />
                            </svg>
                            Conectar Google
                        </template>
                    </Button>
                </div>

                <div class="flex flex-wrap justify-center gap-6">
                    <Link
                        href="/deck/improve"
                        class="text-sm font-semibold text-gray-600 transition hover:text-blue-600"
                    >
                        ✨ Melhorar Decks
                    </Link>

                    <Link
                        href="/note/improve"
                        class="text-sm font-semibold text-gray-600 transition hover:text-blue-600"
                    >
                        📝 Melhorar Nota
                    </Link>
                </div>
            </CardContent>

            <CardFooter
                class="grid grid-cols-1 gap-8 rounded-b-xl border-t border-gray-100 bg-gray-50/50 p-10 text-left sm:grid-cols-3"
            >
                <div class="space-y-2">
                    <h3 class="flex items-center gap-2 font-bold text-gray-900">
                        <span class="text-xl">⚡</span> Rápido
                    </h3>
                    <p class="text-sm leading-relaxed text-gray-600">
                        Gere decks completos em segundos, sem esforço manual.
                    </p>
                </div>

                <div class="space-y-2">
                    <h3 class="flex items-center gap-2 font-bold text-gray-900">
                        <span class="text-xl">🧠</span> Inteligente
                    </h3>
                    <p class="text-sm leading-relaxed text-gray-600">
                        IA identifica conceitos importantes automaticamente.
                    </p>
                </div>

                <div class="space-y-2">
                    <h3 class="flex items-center gap-2 font-bold text-gray-900">
                        <span class="text-xl">🔄</span> Integrado
                    </h3>
                    <p class="text-sm leading-relaxed text-gray-600">
                        Envie direto para o Anki sem complicação.
                    </p>
                </div>
            </CardFooter>
        </Card>

        <footer class="mt-8 text-sm text-gray-400">
            © 2026 FlashIA • Estude melhor, não mais difícil
        </footer>
    </div>
</template>
