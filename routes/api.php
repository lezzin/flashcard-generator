<?php

use App\Http\Controllers\FlashcardController;
use App\Http\Controllers\SummaryController;
use Illuminate\Support\Facades\Route;

Route::prefix('summary')->group(function () {
    Route::post('generate', [SummaryController::class, 'generate']);
});

Route::prefix('flashcard')->group(function () {
    Route::post('generate', [FlashcardController::class, 'generate']);
});
