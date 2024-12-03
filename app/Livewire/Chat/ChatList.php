<?php

namespace App\Livewire\Chat;

use App\Models\Conversation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatList extends Component
{
    public $selectedConversation;
    public $query;

    protected $listeners = ['chat.chat-list' => 'refreshList'];

    public function refreshList()
    {
        // Refresh the list of conversations
        $this->selectedConversation = null;
        $this->query = '';
    }

    public function render()
    {
        return view('livewire.chat.chat-list', [
            // Fetching the conversations
            'conversations' => auth()->user()->conversation()->latest('updated_at')->get()
        ]);
    }

   public function deleteByUser($id) {
    // dd($id,decrypt($id));

    $userId= Auth::user()->id;
    $conversation= Conversation::find(decrypt($id));




    $conversation->messages()->each(function($message) use($userId){

        if($message->sender_id===$userId){

            $message->update(['sender_deleted_at'=>now()]);
        }
        elseif($message->receiver_id===$userId){

            $message->update(['receiver_deleted_at'=>now()]);
        }


    } );


    $receiverAlsoDeleted =$conversation->messages()
            ->where(function ($query) use($userId){

                $query->where('sender_id',$userId)
                      ->orWhere('receiver_id',$userId);

            })->where(function ($query) use($userId){

                $query->whereNull('sender_deleted_at')
                        ->orWhereNull('receiver_deleted_at');

            })->doesntExist();



    if ($receiverAlsoDeleted) {

        $conversation->forceDelete();
    }



    return redirect(route('chat.index'));



   }

}
