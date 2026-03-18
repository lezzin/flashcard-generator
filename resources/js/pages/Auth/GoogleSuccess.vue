<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import Button from '@/components/ui/button.vue';
import Card from '@/components/ui/card/Card.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
</script>

<template>
    <Head title="Sucesso - Google Conectado" />

    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gray-50 px-4"
    >
        <Card class="w-full max-w-md border-gray-200 text-center shadow-xl">
            <CardContent class="flex flex-col items-center space-y-8 p-10">
                <div class="flex flex-col items-center">
                    <div
                        class="mb-6 flex h-20 w-20 items-center justify-center rounded-full bg-green-100 text-green-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-10 w-10"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M5 13l4 4L19 7"
                            />
                        </svg>
                    </div>

                    <h1 class="text-3xl font-extrabold text-gray-900">
                        Google Conectado!
                    </h1>

                    <p class="mt-4 text-lg text-gray-600">
                        Sua conta do Google foi vinculada com sucesso ao
                        FlashIA. Agora você já pode exportar seus flashcards.
                    </p>
                </div>

                <div class="w-full pt-2">
                    <Link href="/" class="block w-full">
                        <Button
                            class="w-full justify-center py-6 text-lg font-semibold shadow-md transition-all hover:scale-[1.02]"
                        >
                            🏠 Voltar para o Início
                        </Button>
                    </Link>
                </div>
            </CardContent>
        </Card>

        <footer class="mt-8 text-sm text-gray-400">
            © 2026 FlashIA • Conectado com segurança
        </footer>
    </div>
</template>
