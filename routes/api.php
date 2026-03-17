<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\CategoryController;
use Illuminate\Support\Facades\Route;

Route::apiResource('notes', NoteController::class);

Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);

Route::patch('notes/actions/archive-old-drafts', [NoteController::class, 'archiveOldDrafts']);

Route::get('users/{user}/notes', [NoteController::class, 'userNotesWithCategories']);

Route::get('notes-actions/search', [NoteController::class, 'search']);

Route::get('users/{user}/notes/pinned', [NoteController::class, 'userPinnedNotes']);

Route::apiResource('categories', CategoryController::class);

Route::patch('notes/{id}/pin', [NoteController::class, 'pin']);
Route::patch('notes/{id}/unpin', [NoteController::class, 'unpin']);

Route::patch('notes/{id}/publish', [NoteController::class, 'publish']);
Route::patch('notes/{id}/archive', [NoteController::class, 'archive']);

