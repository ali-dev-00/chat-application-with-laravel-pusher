<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable=[
        'receiver_id',
        'sender_id'
    ];

    public function messages(){
       return $this->hasMany(Message::class);
    }
    public function getReceiver()
    {
        // Check if the current user is the sender
        if ($this->sender_id === Auth::user()->id) {
            // Return the user who is the receiver
            return User::find($this->receiver_id);
        } else {
            // Return the user who is the sender
            return User::find($this->sender_id);
        }
    }

    public  function unreadMessagesCount() : int {


        return $unreadMessages= Message::where('conversation_id','=',$this->id)
                                    ->where('receiver_id', Auth::user()->id)
                                    ->whereNull('read_at')->count();

        }
    public  function isLastMessageReadByUser():bool {


        $user=Auth::user();
        $lastMessage= $this->messages()->latest()->first();

        if($lastMessage){
            return  $lastMessage->read_at !==null && $lastMessage->sender_id == $user->id;
        }

    }


}
