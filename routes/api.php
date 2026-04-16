<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\NoteController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
        Route::post('/change-password', [AuthController::class, 'changePassword']);
        Route::put('/update-profile', [AuthController::class, 'updateProfile']);
        Route::post('/me/profile-photo', [AuthController::class, 'storeProfilePhoto']);
        Route::delete('/me/profile-photo', [AuthController::class, 'destroyProfilePhoto']);
    });

    Route::middleware('throttle:5,1')
        ->post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:sanctum', 'verified'])
        ->get('/verified', function () {
        return 'ok';
    });
});

Route::middleware('auth:sanctum')->group(function () {
    // všetci prihlásení môžu čítať kategórie
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);

    // iba admin môže vytvárať, upravovať, mazať kategórie
    Route::middleware('admin')->group(function () {
        Route::apiResource('categories', CategoryController::class)->except(['index', 'show']);
    });

    Route::apiResource('notes', NoteController::class);

    Route::get('notes/stats/status', [NoteController::class, 'statsByStatus']);
    Route::patch('notes/actions/archive-old-drafts', [NoteController::class, 'archiveOldDrafts']);
    Route::get('users/{user}/notes', [NoteController::class, 'userNotesWithCategories']);
    Route::get('notes-actions/search', [NoteController::class, 'search']);
    Route::get('users/{user}/notes/pinned', [NoteController::class, 'userPinnedNotes']);

    Route::patch('notes/{id}/pin', [NoteController::class, 'pin']);
    Route::patch('notes/{id}/unpin', [NoteController::class, 'unpin']);
    Route::patch('notes/{id}/publish', [NoteController::class, 'publish']);
    Route::patch('notes/{id}/archive', [NoteController::class, 'archive']);

    Route::get('notes/{note}/attachments', [AttachmentController::class, 'index']);

    Route::middleware('premium')->group(function () {
        Route::post('notes/{note}/attachments', [AttachmentController::class, 'store']);
    });

    Route::get('/attachments/{attachment:public_id}/link', [AttachmentController::class, 'link']);
    Route::delete('/attachments/{attachment:public_id}', [AttachmentController::class, 'destroy']);

    Route::apiResource('notes.tasks', TaskController::class)->scoped();

    Route::get('notes/{note}/comments', [CommentController::class, 'indexForNote']);
    Route::post('notes/{note}/comments', [CommentController::class, 'storeForNote']);

    Route::get('notes/{note}/tasks/{task}/comments', [CommentController::class, 'indexForTask']);
    Route::post('notes/{note}/tasks/{task}/comments', [CommentController::class, 'storeForTask']);

    Route::patch('comments/{id}', [CommentController::class, 'update']);
    Route::delete('comments/{id}', [CommentController::class, 'destroy']);
});
