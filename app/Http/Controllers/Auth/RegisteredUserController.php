<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */


     public function store(Request $request): RedirectResponse
     {
         // Validate input
         $validatedData = $request->validate([
             'name' => ['required', 'string', 'max:255'],
             'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
             'password' => ['required', 'confirmed', Rules\Password::defaults()],
             'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Validate image
         ]);

         // Handle image upload if provided
         if ($request->hasFile('image')) {
             $imagePath = $request->file('image')->store('profile_images', 'public');
             $validatedData['image'] = $imagePath;
         }

         // Create user
         $user = User::create([
             'name' => $validatedData['name'],
             'email' => $validatedData['email'],
             'password' => Hash::make($validatedData['password']),
             'image' => $validatedData['image'] ?? null, // Save image path if available
         ]);

         event(new Registered($user));

         Auth::login($user);

         return redirect(route('dashboard'));
     }


}
