<div
x-data="{type:'all',query:@entangle('query')}"
x-init="

setTimeout(()=>{

 conversationElement = document.getElementById('conversation-'+query);


 //scroll to the element

 if(conversationElement)
 {

     conversationElement.scrollIntoView({'behavior':'smooth'});

 }

 },200);


     Echo.private('users.{{Auth()->User()->id}}')
    .notification((notification)=>{
        if(notification['type']== 'App\\Notifications\\MessageRead'||notification['type']== 'App\\Notifications\\MessageSent')
        {

            window.Livewire.emit('refresh');
        }
    });
 "
  class="flex flex-col h-full overflow-hidden transition-all">

    <header class="sticky top-0 z-10 w-full px-3 py-2 bg-white">

        <div class="flex items-center justify-between pb-2 border-b">

            <div class="flex items-center gap-2">
                <h5 class="text-2xl font-extrabold">Chats</h5>
            </div>

            <button>

                <svg class="w-7 h-7" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    viewBox="0 0 16 16">
                    <path
                        d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z" />
                </svg>

            </button>

        </div>

        {{-- Filters --}}
        <div class="flex items-center gap-3 p-2 overflow-x-hidden bg-white">

            <button @click="type='all'" :class="{'bg-blue-300 border-0 text-black':type=='all'}"
                class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium border rounded-full gap-x-1 lg:px-4 lg:py-2 ">
                All
            </button>
            <button @click="type='deleted'" :class="{'bg-blue-300 border-0 text-black':type=='deleted'}"
                class="inline-flex items-center justify-center px-3 py-1 text-xs font-medium border rounded-full gap-x-1 lg:px-4 lg:py-2 ">
                Deleted
            </button>

        </div>

    </header>


    <main class="relative h-full overflow-hidden overflow-y-auto grow" style="contain:content">

        {{-- chatlist --}}

        <ul class="grid w-full p-2 spacey-y-2">

            @if($conversations)


             @foreach ($conversations as $key => $conversation)




            <li
            id="conversation-{{$conversation->id}}" wire:key="{{$conversation->id}}"
            class="py-3 hover:bg-gray-50 rounded-2xl dark:hover:bg-gray-300 transition-colors duration-150 flex gap-4 relative w-full cursor-pointer px-2 {{$conversation->id==$selectedConversation?->id ? 'bg-gray-100/70':''}}">
                <a href="#" class="shrink-0">
                    <x-avatar />
                    
                </a>

                <aside class="grid w-full grid-cols-12">

                    <a href="{{route('chat',$conversation->id)}}"
                      class="relative w-full col-span-11 p-1 pb-2 overflow-hidden leading-5 truncate border-b border-gray-200 flex-nowrap">

                        {{-- name and date --}}
                        <div class="flex items-center justify-between w-full">

                            <h6 class="font-medium tracking-wider text-gray-900 truncate">
                                {{ $conversation->getReceiver()->name }}
                            </h6>

                            <small class="text-gray-700">{{$conversation->messages?->last()?->created_at?->shortAbsoluteDiffForHumans()}} </small>

                        </div>

                        {{-- Message body --}}


                        <div class="flex items-center gap-x-2">

                            @if ($conversation->messages?->last()?->sender_id==auth()->id())

                            @if ($conversation->isLastMessageReadByUser())
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-check2-all" viewBox="0 0 16 16">
                                    <path
                                        d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7zm-4.208 7-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z" />
                                    <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708z" />
                                </svg>
                            </span>
                            @else


                            {{-- single tick --}}
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-check2" viewBox="0 0 16 16">
                                    <path
                                        d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" />
                                </svg>
                            </span>
                            @endif

                            @endif








                            <p class="grow truncate text-sm font-[100]">
                                {{ $conversation->messages?->last()?->body ?? '   ' }}
                            </p>


                            {{-- unread count --}}
                            @if ($conversation->unreadMessagesCount()>0)
                             <span class="p-px px-2 text-xs font-bold text-white bg-blue-500 rounded-full shrink-0">
                                {{$conversation->unreadMessagesCount()}}
                             </span>

                             @endif



                        </div>



                    </a>

                    {{-- Dropdown --}}

                    <div class="flex flex-col col-span-1 my-auto text-center">

                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button>

                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                        class="text-gray-700 bi bi-three-dots-vertical w-7 h-7" viewBox="0 0 16 16">
                                        <path
                                            d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                    </svg>


                                </button>
                            </x-slot>

                            <x-slot name="content">

                                <div class="w-full p-1">


                                    <button
                                     onclick="confirm('Are you sure?')||event.stopImmediatePropagation()"
                                    wire:click="deleteByUser('{{encrypt($conversation->id)}}')"
                                        class="flex items-center w-full gap-3 px-4 py-2 text-sm leading-5 text-left text-gray-500 transition-all duration-150 ease-in-out hover:bg-gray-100 focus:outline-none focus:bg-gray-100">

                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                fill="currentColor" class="bi bi-trash-fill" viewBox="0 0 16 16">
                                                <path
                                                    d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z" />
                                            </svg>
                                        </span>

                                        Delete

                                    </button>



                                </div>

                            </x-slot>
                        </x-dropdown>



                    </div>


                </aside>

            </li>

            @endforeach
            @else

            @endif
        </ul>

    </main>
</div>
