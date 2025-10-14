<?php

use App\Events\WhatsappDelivered;
use App\Events\WhatsappNewMessage;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\EmpresasController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StartConversationController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\WhatsappWebhook;
use App\Models\Conversations;
use App\Models\Messages;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });

Route::get('/', function () {
    return Inertia::render('Dashboard', [
        'init' => [
            'logo' => asset('storage/logoWhatsPow.png'),
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard',[
        'init' => [
            'logo' => asset('storage/logoWhatsPow.png'),
        ],
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/conversations', [WhatsappController::class, 'conversations'])->name('conversations');
    Route::post('/send_message', [WhatsappController::class, 'send_message'])->name('send_message');
    Route::post('/mark_message_as_read', [WhatsappController::class, 'mark_message_as_read'])->name('mark_message_as_read');
    Route::get('/get_messages', [WhatsappController::class, 'get_messages'])->name('get_messages');
    Route::get('/get_resume', [WhatsappController::class, 'get_resume'])->name('get_resume');
    Route::post('/close_conversation', [WhatsappController::class, 'close_conversation'])->name('close_conversation');
    Route::get('/search_empresa', [EmpresasController::class, 'search'])->name('search_empresa');
    Route::get('/test', function(){
        $message = Conversations::find(31);
        broadcast(new WhatsappNewMessage($message))->via('reverb');
        return 'ok';
    });
    Route::get('get_conversation', [ConversationController::class, 'get_conversation'])->name('get_conversation');
    Route::resource('start_conversation', StartConversationController::class);
});

Route::get('/send_message_test', [WhatsappController::class, 'send_message_test'])->name('whatsapp.send_message_test');
Route::get('/webhook', [WhatsappController::class, 'set'])->name('webhook.set');
// Route::post('/send_message', [WhatsappController::class, 'send_message'])->name('whatsapp.send_message');
Route::post('/webhook', WhatsappWebhook::class);


require __DIR__.'/auth.php';
