<script setup lang="ts">
import { Link, router } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation Bar -->
        <nav class="bg-white border-b border-gray-100 shadow-sm">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 justify-between items-center">
                    <div class="flex shrink-0 items-center">
                        <Link href="/" class="text-xl font-bold text-blue-600">
                            Flashcard AI
                        </Link>
                    </div>

                    <div class="hidden sm:ml-6 sm:flex sm:space-x-8">
                        <Link href="/flashcard/generate"
                            class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium">
                            Generate
                        </Link>

                        <Link href="/flashcard/improve"
                            class="border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700inline-flex items-center border-b-2 px-1 pt-1 text-sm font-medium">
                            Improve
                        </Link>
                    </div>
                    <div class="flex items-center">
                        <span class="text-xs text-gray-400">v1.0.0</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="py-12">
            <slot />
        </main>
    </div>
</template>
