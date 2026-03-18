<?php

use App\Http\Controllers\DeckController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\ContentController;
use Illuminate\Support\Facades\Route;

Route::prefix('contents')->group(function () {
    Route::post('/', [ContentController::class, 'store']);
});

Route::prefix('flashcards')->group(function () {
    Route::post('/', [FlashcardController::class, 'store']);
});

Route::prefix('decks')->group(function () {
    Route::get('/', [DeckController::class, 'index']);
    Route::post('export', [DeckController::class, 'export']);
    Route::post('improve', [DeckController::class, 'improve']);
});

Route::prefix('notes')->group(function () {
    Route::get('/', [NoteController::class, 'index']);
    Route::post('improve', [NoteController::class, 'improve']);
});

Route::prefix('google')->group(function () {
    Route::get('auth', [GoogleController::class, 'auth']);
    Route::get('callback', [GoogleController::class, 'callback']);
});
