<?php

use App\Http\Controllers\GoogleController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/flashcard/generate', 'Flashcard/Generate')->name('flashcard.generate');
Route::inertia('/deck/improve', 'Flashcard/ImproveDeck')->name('deck.improve');
Route::inertia('/note/improve', 'Flashcard/ImproveNote')->name('note.improve');

Route::inertia('/files', 'Files/Index')->name('files.index');

Route::prefix('google')->group(function () {
    Route::get('callback', [GoogleController::class, 'callback']);
});
