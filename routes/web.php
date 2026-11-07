<?php

use App\Http\Controllers\AttachmentController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\WatcherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    // Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class);
    Route::get('/tickets/assigned-to-me', [TicketController::class, 'assignedToMe'])->name('tickets.assigned-to-me');
    Route::get('/tickets/created-by-me', [TicketController::class, 'createdByMe'])->name('tickets.created-by-me');
    Route::get('/tickets/watched', [TicketController::class, 'watchedByMe'])->name('tickets.watched');
    Route::resource('tickets', TicketController::class);
    Route::patch('/tickets/{id}/restore', [TicketController::class, 'restore'])->name('tickets.restore');
    Route::patch('/tickets/{ticket}/transition', [TicketController::class, 'transitionStatus'])->name('tickets.transition');
    Route::patch('/tickets/{ticket}/reassign', [TicketController::class, 'reassign'])->name('tickets.reassign');
    Route::patch('/tickets/{ticket}/priority', [TicketController::class, 'changePriority'])->name('tickets.priority');
    Route::post('/tickets/{ticket}/watch', [WatcherController::class, 'toggle'])->name('tickets.watch');
    Route::post('/tickets/{ticket}/comments', [CommentController::class, 'store'])->name('comments.store');
    Route::patch('/comments/{comment}', [CommentController::class, 'update'])->name('comments.update');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

    // Attachments (Secure streaming & thumbnailing)
    Route::get('/attachments/{attachment}', [AttachmentController::class, 'show'])->name('attachments.show');
    Route::get('/attachments/{attachment}/thumb', [AttachmentController::class, 'thumbnail'])->name('attachments.thumb');
    Route::delete('/attachments/{attachment}', [AttachmentController::class, 'destroy'])->name('attachments.destroy');
});

require __DIR__.'/auth.php';
