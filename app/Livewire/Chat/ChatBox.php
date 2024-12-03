<?php

namespace App\Livewire\Chat;

use App\Models\Message;
use Livewire\Component;
use App\Notifications\MessageRead;
use App\Notifications\MessageSent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ChatBox extends Component
{
    public $selectedConversation;
    public $body;
    public $loadedMessages;

    public $paginate_var = 10;

    protected $listeners = [
        'loadMore'
    ];


    public function loadMore() : void
{
    $this->paginate_var += 10;
    $this->loadMessages();

}
   public function loadMessages()
    {
        $count=Message::where('conversation_id',$this->selectedConversation->id)->count();
        $this->loadedMessages=Message::where('conversation_id',$this->selectedConversation->id)
        ->skip($count-$this->paginate_var)
        ->take($this->paginate_var)
        ->get();
         return $this->loadedMessages;
    }
    public function getListeners(){
        $auth_id = Auth::user()->id;
        return[
            'loadMore',
            "echo-private:users.{$auth_id},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotifications'
        ];
    }
    public function broadcastedNotifications($event)
    {


        if ($event['type'] == MessageSent::class) {

            if ($event['conversation_id'] == $this->selectedConversation->id) {
                $this->dispatch('scroll-bottom');

                $newMessage = Message::find($event['message_id']);


                #push message
                $this->loadedMessages->push($newMessage);


                #mark as read
                $newMessage->read_at = now();
                $newMessage->save();

                #broadcast
                $this->selectedConversation->getReceiver()
                    ->notify(new MessageRead($this->selectedConversation->id));
            }
        }
    }

    public function sendMessage()
    {
        $this->validate(['body' => 'required|string']);

        $createdMessage = Message::create([
            'conversation_id' => $this->selectedConversation->id,
            'sender_id' => Auth::user()->id,
            'receiver_id' => $this->selectedConversation->getReceiver()->id,
            'body' => $this->body
        ]);

        $this->reset('body');
        $this->dispatch('scroll-bottom');

        $this->loadedMessages->push($createdMessage);
        $this->selectedConversation->updated_at = now();
        $this->selectedConversation->save();

        $this->dispatch('chat.chat-list', 'refresh');

        $this->selectedConversation->getReceiver()->notify(new MessageSent(
            Auth::user(),
            $createdMessage,
            $this->selectedConversation,
            $this->selectedConversation->getReceiver()->id
        ));
    }


    public function mount()
    {

        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }

}
