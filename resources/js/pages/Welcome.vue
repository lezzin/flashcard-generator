<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import PrimaryButton from '@/components/PrimaryButton.vue';
</script>

<template>
    <Head title="Welcome to Flashcard Generator" />

    <div class="min-h-screen bg-white text-gray-900">
        <!-- Hero Section -->
        <main class="mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            <div class="text-center">
                <h1
                    class="text-4xl font-bold tracking-tight text-gray-900 sm:text-6xl"
                >
                    Master any subject with AI-powered Flashcards
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-600">
                    Generate high-quality Anki cards from your study notes in
                    seconds. Automate the tedious work and focus on learning.
                </p>
                <div class="mt-10 flex items-center justify-center gap-6">
                    <Link href="/flashcard/generate">
                        <PrimaryButton class="px-8 py-3 text-base">
                            Get Started
                        </PrimaryButton>
                    </Link>
                    <Link
                        href="/flashcard/improve"
                        class="text-sm leading-6 font-semibold text-gray-900 transition hover:text-blue-600"
                    >
                        Optimize Existing Decks
                        <span aria-hidden="true">→</span>
                    </Link>
                </div>
            </div>

            <!-- Features -->
            <div
                class="mt-32 grid grid-cols-1 gap-12 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    class="rounded-2xl border border-gray-100 p-8 shadow-sm transition hover:shadow-md"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-600 text-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4.5v15m7.5-7.5h-15"
                            />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-gray-900">
                        Instant Generation
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Paste your notes, PDFs, or articles and get a structured
                        deck ready for Anki instantly.
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-gray-100 p-8 shadow-sm transition hover:shadow-md"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-600 text-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.042 21.672L13.684 16.6m0 0l-2.51 2.225.569-9.47 5.227 7.917-3.286-.672zm-7.518-.267A8.25 8.25 0 1120.25 10.5M8.288 14.212A5.25 5.25 0 1113.5 15.75"
                            />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-gray-900">
                        Smart Highlights
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Our AI automatically identifies and bolds keywords to
                        leverage the Cloze deletion effect.
                    </p>
                </div>

                <div
                    class="rounded-2xl border border-gray-100 p-8 shadow-sm transition hover:shadow-md sm:col-span-2 lg:col-span-1"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-600 text-white"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M19.5 12c0-1.232-.046-2.453-.138-3.662a4.006 4.006 0 00-3.7-3.7 48.678 48.678 0 00-7.324 0 4.006 4.006 0 00-3.7 3.7c-.017.22-.032.441-.046.662M19.5 12l3-3m-3 3l-3-3m-12 3c0 1.232.046 2.453.138 3.662a4.006 4.006 0 003.7 3.7 48.656 48.656 0 007.324 0 4.006 4.006 0 003.7-3.7c.017-.22.032-.441.046-.662M4.5 12l3 3m-3-3l-3 3"
                            />
                        </svg>
                    </div>
                    <h3 class="mt-6 text-lg font-semibold text-gray-900">
                        Anki Sync
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        Direct integration with AnkiConnect. No manual CSV
                        exports or imports needed.
                    </p>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="mt-32 border-t border-gray-100 py-12">
            <div
                class="mx-auto max-w-7xl px-6 text-center text-sm text-gray-500 lg:px-8"
            >
                <p>
                    &copy; 2026 Flashcard Generator. Built for students who
                    value their time.
                </p>
            </div>
        </footer>
    </div>
</template>
