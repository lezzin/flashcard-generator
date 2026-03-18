<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import Button from '@/components/ui/button.vue';
</script>

<template>
    <div class="min-h-screen bg-gray-50/50 font-sans text-gray-900">
        <nav
            class="sticky top-0 z-40 w-full border-b border-gray-200 bg-white/80 backdrop-blur-md"
        >
            <div class="mx-auto max-w-5xl px-4 sm:px-6">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center gap-8">
                        <Link
                            href="/"
                            class="flex items-center gap-2 text-xl font-bold tracking-tight"
                        >
                            Flash<span class="text-blue-600">IA</span>
                        </Link>

                        <div class="hidden items-center gap-1 md:flex">
                            <Link href="/flashcard/generate">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-gray-600"
                                    >Gerar</Button
                                >
                            </Link>
                            <Link href="/deck/improve">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-gray-600"
                                    >Otimizar Deck</Button
                                >
                            </Link>
                            <Link href="/note/improve">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-gray-600"
                                    >Otimizar Note</Button
                                >
                            </Link>
                            <Link href="/files">
                                <Button
                                    variant="ghost"
                                    size="sm"
                                    class="text-gray-600"
                                    >Arquivos</Button
                                >
                            </Link>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="font-mono text-xs text-gray-400">v1.0</div>
                    </div>
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
