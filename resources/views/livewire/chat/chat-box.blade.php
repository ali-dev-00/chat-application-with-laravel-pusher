<div x-data="{
    conversationElement: null,
    body: @entangle('body'),
    scrollToBottom() {
        this.conversationElement.scrollTop = this.conversationElement.scrollHeight;
    },
    clearInput() {
        this.body = ''; // Clear the Alpine.js input explicitly
    },
    sendMessage() {
        if (this.body.trim() !== '') {
            $wire.sendMessage().then(() => {
                this.clearInput(); // Clear the input only after Livewire sends the message
            });
        }
    }
}"
 x-init="
    conversationElement = $refs.conversation;
    $nextTick(() => scrollToBottom());

    Echo.private('users.{{Auth()->User()->id}}')
        .notification((notification)=>{
            if(notification['type']== 'App\\Notifications\\MessageRead' && notification['conversation_id']== {{$this->selectedConversation->id}})
            {

                markAsRead=true;
            }
        });

" @scroll-bottom.window="
    $nextTick(() => scrollToBottom());

"

class="w-full overflow-hidden">
<div class="flex flex-col h-full overflow-y-auto border-b grow">
    <!-- Conversation Header -->

        <header class="w-full sticky inset-x-0 flex pb-[2px] pt-[5px] top-0 z-10 bg-white border-b ">
            <div class="flex items-center w-full gap-2 px-2 lg:px-4 md:gap-5">
                <a href="#"
                    class="shrink-0 lg:hidden"><svg xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                    </svg>
                </a>{{-- avatar --}}



                    <div class="shrink-0">
                    <x-avatar class="h-9 w-9 lg:w-11 lg:h-11" />
                    </div>
                <h6 class="font-bold truncate">{{ $selectedConversation->getReceiver()->email }}</h6>
            </div>
        </header>


 <!-- Conversation Messages -->
<main x-ref="conversation"
@scroll="
 scropTop = $el.scrollTop;
 if(scropTop <= 0){
  $wire.dispatch('loadMore');
 }
"
@update-chat-height.window="

newHeight= $el.scrollHeight;

oldHeight= height;
$el.scrollTop= newHeight- oldHeight;

height=newHeight;

"
class="flex flex-col gap-3 p-2.5 overflow-y-auto flex-grow overscroll-contain overflow-x-hidden w-full my-auto">
@if ($loadedMessages)
@php
    $previousMessage = null;
@endphp

@foreach ($loadedMessages as $key => $message)
    @php
        // Set the previous message for comparison
        $previousMessage = $key > 0 ? $loadedMessages->get($key - 1) : null;
    @endphp

    <div
    wire:key="{{ time().$key }}"
    @class([
            'max-w-[85%] md:max-w-[78%] flex w-auto gap-2 relative mt-2',
            'ml-auto' => $message->sender_id === auth()->id(),
        ])>
        <!-- Avatar -->
        <div @class([
                'shrink-0',
                'invisible' => $previousMessage?->sender_id == $message->sender_id,
                'hidden'=>$message->sender_id === auth()->id()
            ])>

                <x-avatar />

        </div>

        <!-- Message Body -->
        <div @class([
                'flex flex-wrap text-[15px] rounded-xl p-2.5 flex flex-col text-black bg-[#f6f6f8fb]',
                'rounded-bl-none border-gray-200/40' => $message->sender_id !== auth()->id(),
                'rounded-br-none bg-blue-500/80 text-white' => $message->sender_id === auth()->id(),
            ])>
            <p class="text-sm tracking-wide truncate whitespace-normal md:text-base lg:tracking-normal">
                {{ $message->body }}
            </p>
             <div class="flex gap-2 ml-auto">
            <p @class([
                'text-xs',
                'text-gray-500'=>!($message->sender_id===auth()->id()),
                'text-white'=>$message->sender_id===auth()->id()
            ])>
              {{ $message->created_at->format('g:i a') }}
            </p>

            {{-- message , only if message belongs to auth --}}
            @if ($message->sender_id===auth()->id())

            <div>
                @if ($message->isRead())


                {{-- double ticks --}}
                 <span @class('text-gray-200')>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2-all" viewBox="0 0 16 16">
                        <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z"/>
                        <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708z"/>
                    </svg>
                 </span>

                @else
                {{-- single ticks --}}
                <span @class('text-gray-200')>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check2" viewBox="0 0 16 16">
                        <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                    </svg>
                 </span>
                 @endif
            </div>

            @endif

        </div>
        </div>
    </div>
@endforeach
@endif
</main>

    <!-- Message Input -->
    <footer class="inset-x-0 z-10 bg-white shrink-0">
        <div class="p-2 border-t">
            <form @submit.prevent="sendMessage" method="POST" autocapitalize="off">
                @csrf
                <input type="hidden" autocomplete="false" style="display: none">
                <div class="grid grid-cols-12">
                    <input x-model="body" type="text" autocomplete="off" autofocus
                        placeholder="Write your message here" maxlength="1700"
                        class="col-span-10 bg-gray-100 border-0 rounded-lg outline-0 focus:border-0 focus:ring-0 hover:ring-0 focus:outline-none">
                    <button x-bind:disabled="!body.trim()" class="col-span-2" type="submit">Send</button>
                </div>
            </form>

        </div>
    </footer>
</div>
</div>
