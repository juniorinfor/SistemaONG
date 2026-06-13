<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DocumentTypeController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\ChecklistController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\TransparencyController;
use Illuminate\Support\Facades\Route;

// Raiz → login ou dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Área autenticada
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('documents', DocumentController::class);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::resource('document-types', DocumentTypeController::class);
    Route::resource('people',         PersonController::class);
    Route::resource('checklists',     ChecklistController::class);

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Perfil Breeze
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Portal público (sem login)
Route::get('/transparencia',          [TransparencyController::class, 'index'])->name('transparency.index');
Route::get('/transparencia/documento/{document}/download',
    [TransparencyController::class, 'download'])->name('transparency.download');

require __DIR__.'/auth.php';
