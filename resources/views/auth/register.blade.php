<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* Hide the file input */
    #picture__input {
        display: none;
    }

    /* Style for the profile picture container */
    .picture {
        width: 120px;
        height: 120px;
        background: #f4f4f4;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        border: 1px solid lightgray;
        cursor: pointer;
        font-family: sans-serif;
        transition: color 300ms ease-in-out, background 300ms ease-in-out;
        outline: none;
        overflow: hidden;
        border-radius: 50%;
        /* Make the container circular */
        position: relative;
        /* Ensure the container is positioned relative to place icon */
    }

    /* Hover effect */
    .picture:hover {
        color: #777;
        background: #ccc;
    }

    /* Active effect */
    .picture:active {
        border-color: turquoise;
        color: turquoise;
        background: #eee;
    }

    /* Focus effect */
    .picture:focus {
        color: #777;
        background: #ccc;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
    }

    /* Style for the image inside the container */
    .picture__image {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        position: relative;
    }

    /* Style for the image itself */
    .picture__img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        /* Ensure the image covers the container without distortion */
        border-radius: 50%;
        /* Ensure the image fits within the circular container */
    }

    /* Style for the icon */
    .icon {
        position: absolute;
        top: 10px;
        /* Position the icon at the bottom */
        left: 57.5%;
        /* Position the icon on the right */
        width: 24px;
        /* Adjust icon size */
        height: 24px;
        /* Adjust icon size */
        color: #555;
        z-index: 99999;
        /* Icon color */
        background: #000;
        /* Light background behind the icon */
        border-radius: 50%;
        /* Circular background */
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        /* Ensure the cursor is a pointer */
    }
</style>




<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
        @csrf
        <!-- Profile Image -->
        <div class="flex items-center mt-4" style="display: flex; justify-content: center; position: relative;">
            <div class="p-1 text-white bg-dark icon" style="background: #040404; border-radius:50%">
                <i class="fa-solid fa-plus"></i>
            </div>
            <label class="picture" for="picture__input" tabIndex="0">

                <div class="picture__image">

                </div>
            </label>
            <input type="file" name="image" id="picture__input" accept="image/*">
        </div>
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block w-full mt-1" type="text" name="name" :value="old('name')" required
                autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block w-full mt-1" type="email" name="email" :value="old('email')" required
                autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block w-full mt-1" type="password" name="password" required
                autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block w-full mt-1" type="password"
                name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        const inputFile = document.querySelector("#picture__input");
        const pictureImage = document.querySelector(".picture__image");
        const pictureImageTxt = "Choose image";
        pictureImage.innerHTML = pictureImageTxt;

        inputFile.addEventListener("change", function (e) {
            const inputTarget = e.target;
            const file = inputTarget.files[0];

            if (file) {
                const reader = new FileReader();

                reader.addEventListener("load", function (e) {
                    const readerTarget = e.target;

                    const img = document.createElement("img");
                    img.src = readerTarget.result;
                    img.classList.add("picture__img");

                    pictureImage.innerHTML = "";
                    pictureImage.appendChild(img);
                });

                reader.readAsDataURL(file);
            } else {
                pictureImage.innerHTML = pictureImageTxt;
            }
        });

    </script>
</x-guest-layout>
