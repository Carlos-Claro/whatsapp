<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\WhatsappWebhook;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/webhook', [WhatsappController::class, 'set'])->name('webhook.set');
Route::post('/send_message', [WhatsappController::class, 'send_message'])->name('whatsapp.send_message');
Route::post('/webhook', WhatsappWebhook::class);
Route::get('conversations', [WhatsappController::class, 'conversations'])->name('whatsapp.conversations');

require __DIR__.'/auth.php';
