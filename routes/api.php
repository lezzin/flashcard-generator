<?php

use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\SummaryController;
use Illuminate\Support\Facades\Route;

Route::prefix('summary')->group(function () {
    Route::post('generate', [SummaryController::class, 'generate']);
});

Route::prefix('flashcard')->group(function () {
    Route::post('generate', [FlashcardController::class, 'generate']);
    Route::post('reprocess', [FlashcardController::class, 'reprocess']);
    Route::post('improve', [FlashcardController::class, 'improve']);

    Route::prefix('deck')->group(function () {
        Route::get('/', [FlashcardController::class, 'getDeckNames']);
        Route::get('notes', [FlashcardController::class, 'findNotes']);
    });
});
