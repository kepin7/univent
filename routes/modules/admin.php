<?php

use App\Http\Controllers\Admin\EventListController;
use Illuminate\Support\Facades\Route;

// Semua route admin pakai prefix “admin” + middleware auth & admin
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    // 1. Halaman daftar event untuk admin
    Route::get('/event-list', [EventListController::class, 'index'])
        ->name('admin.event-list');

    // 2. Detail event
    Route::get('/event-list/{id}', [EventListController::class, 'show'])
        ->name('admin.events.detail');

    // 3. Approve event
    Route::post('/event-list/{id}/approve', [EventListController::class, 'approve'])
        ->name('admin.events.approve');

    // 4. Reject event
    Route::post('/event-list/{id}/reject', [EventListController::class, 'reject'])
        ->name('admin.events.reject');
    // delete event
    Route::delete('/admin/events/{id}/delete', [App\Http\Controllers\Admin\EventListController::class, 'delete'])
        ->name('admin.events.delete');
});