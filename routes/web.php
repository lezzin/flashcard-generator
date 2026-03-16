<?php

use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::inertia('/flashcard/generate', 'Flashcard/Generate')->name('flashcard.generate');
Route::inertia('/flashcard/improve', 'Flashcard/Improve')->name('flashcard.improve');
