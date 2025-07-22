<?php

use App\Http\Controllers\Settings;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ArticleController;

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::get('settings/profile', [Settings\ProfileController::class, 'edit'])->name('settings.profile.edit');
    Route::put('settings/profile', [Settings\ProfileController::class, 'update'])->name('settings.profile.update');
    Route::delete('settings/profile', [Settings\ProfileController::class, 'destroy'])->name('settings.profile.destroy');
    Route::get('settings/password', [Settings\PasswordController::class, 'edit'])->name('settings.password.edit');
    Route::put('settings/password', [Settings\PasswordController::class, 'update'])->name('settings.password.update');
    Route::get('settings/appearance', [Settings\AppearanceController::class, 'edit'])->name('settings.appearance.edit');

    // Articles
    Route::resource('articles', ArticleController::class)->except(['show']);

    // Actions de workflow
    Route::patch('/articles/{article}/submit', [ArticleController::class, 'submit'])->name('articles.submit');
    Route::patch('/articles/{article}/approve', [ArticleController::class, 'approve'])->name('articles.approve');
    Route::patch('/articles/{article}/reject', [ArticleController::class, 'reject'])->name('articles.reject');
});

Route::get('/public/articles', [ArticleController::class, 'publicIndex'])->name('articles.public');

require __DIR__ . '/auth.php';
