<?php

use App\Models\User;
use App\Livewire\Users;
use App\Models\Message;
use App\Events\TestEvent;
use App\Livewire\Chat\Chat;
use App\Livewire\Chat\Index;
use App\Models\Conversation;
use App\Notifications\MessageSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function(){
    Route::get('/chat', Index::class)->name('chat.index');
    Route::get('/chat/{query}', Chat::class)->name('chat');
    Route::get('/users', Users::class)->name('users');
});



Route::get('/test-broadcast', function () {
    broadcast(new TestEvent())->toOthers();

    return 'Event broadcasted!';
});
Route::get('/test-notification', function () {
    $user = User::find(1);
    $receiver = User::find(2);

    $message = Message::create([
        'conversation_id' => 1,
        'sender_id' => $receiver->id,
        'receiver_id' => $user->id,
        'body' => 'This is a test message.',
    ]);

    $conversation = Conversation::find(1);

    try {
        $user->notify(new MessageSent(
            $receiver,
            $message,
            $conversation,
            $receiver->id
        ));

        return 'Notification sent and event triggered successfully!';
    } catch (\Exception $e) {
        return 'Error sending notification: ' . $e->getMessage();
    }
});
require __DIR__.'/auth.php';
