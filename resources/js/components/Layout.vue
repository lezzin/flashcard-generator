<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-50 font-sans text-gray-900">
        <nav class="border-b border-gray-200 bg-white">
            <div class="mx-auto max-w-5xl px-4 sm:px-6">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-8">
                        <Link href="/" class="text-xl font-bold tracking-tight text-blue-600">
                            FlashIA
                        </Link>

                        <div class="flex gap-4">
                            <Link href="/flashcard/generate"
                                class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">
                                Gerar
                            </Link>
                            <Link href="/deck/improve"
                                class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">
                                Otimizar Deck
                            </Link>
                            <Link href="/note/improve"
                                class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">
                                Otimizar Note
                            </Link>
                            <Link href="/files"
                                class="text-sm font-medium text-gray-600 hover:text-blue-600 transition">
                                Arquivos
                            </Link>
                        </div>
                    </div>
                    <div class="text-xs text-gray-400 font-mono">v1.0</div>
                </div>
            </div>
        </nav>

        <main class="py-10">
            <div class="mx-auto max-w-5xl px-4 sm:px-6">
                <slot />
            </div>
        </main>
    </div>
</template>
