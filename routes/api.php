<?php

use App\Http\Controllers\ContentController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\DeckController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\GoogleController;
use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::prefix('contents')->group(function () {
    Route::post('/', [ContentController::class, 'store']);
});

Route::prefix('flashcards')->group(function () {
    Route::post('/', [FlashcardController::class, 'store']);
    Route::post('/add', [FlashcardController::class, 'addToAnki']);
});

Route::prefix('decks')->group(function () {
    Route::get('/', [DeckController::class, 'index']);
    Route::post('export', [DeckController::class, 'export']);
    Route::post('improve', [DeckController::class, 'improve']);
    Route::post('generate', [DeckController::class, 'generate']);
});

Route::prefix('notes')->group(function () {
    Route::get('/', [NoteController::class, 'index']);
    Route::post('improve', [NoteController::class, 'improve']);
});

Route::prefix('google')->group(function () {
    Route::get('auth', [GoogleController::class, 'auth']);
});

Route::prefix('files')->group(function () {
    Route::get('/', [FileController::class, 'index']);
    Route::get('/{id}/download', [FileController::class, 'download'])->name('files.download');
});

Route::prefix('database')->group(function () {
    Route::get('/notes', [DatabaseController::class, 'getNotes']);
    Route::get('/contents', [DatabaseController::class, 'getGeneratedContents']);
});
