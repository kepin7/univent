<?php

use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;

Route::get(uri: '/browse-events', action: [EventController::class, 'browse'])->name('browse-events');
Route::get('/events/{id}', [EventController::class, 'show'])->name('events.show');

Route::middleware('auth')->group(function () {

    // Form submit event (create)
    Route::get('/submit-event', [EventController::class, 'create'])
        ->name('submit-event.form');

    // Simpan event baru
    Route::post('/submit-event', [EventController::class, 'store'])
        ->name('submit-event');

    // Update event
    Route::put('/submit-event/{id}', [EventController::class, 'update'])
        ->name('submit-event.update');

    // Riwayat event user
    Route::get('/event-history', [EventController::class, 'showHistory'])
        ->name('user.event.history');

    // Detail registrasi user
    Route::get('/registration/{id}', [EventController::class, 'showRegistration'])
        ->name('registration.show');
});