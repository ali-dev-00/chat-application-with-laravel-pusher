<div class="max-w-6xl mx-auto my-16">

    <div class="grid gap-5 p-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 ">

        @foreach ($users as $key=> $user)



        {{-- child --}}
        <div class="w-full p-4 bg-white border border-gray-200 rounded-lg shadow">

            <div class="flex flex-col items-center pb-2">

                <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('images/default.png') }}"  alt="image" class="rounded-full shadow-lg h-28 mb- w-28 5">

                <h5 class="mt-2 font-medium text-gray-900 " >
                    {{$user->name}}
                </h5>
                <span class="text-sm text-gray-500">{{$user->email}} </span>

                <div class="flex mt-3 md:mt-6">



                    <x-primary-button wire:click="message({{$user->id}})" class="w-100" >
                        Message
                    </x-primary-button>

                </div>

            </div>


        </div>

        @endforeach
    </div>




</div>
