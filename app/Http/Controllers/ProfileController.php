<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        // Get the currently authenticated user
        $user = Auth::user();

        // Return the user data
        return response()->json([
            'message' => 'Profile retrieved successfully.',
            'user' => $user
        ]);
    }

    public function update(Request $request)
    {
        // Get the currently logged-in user
        $user = Auth::user();

        // Validate the incoming data
        $validatedData = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],

            // THE EMAIL FIX:
            // Tell Laravel it must be unique in the 'users' table,
            // EXCEPT for the row matching this $user->id
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],

            // THE PASSWORD FIX:
            // Make it 'nullable' so it doesn't fail validation if left blank
            'password' => [
                'nullable',
                'string',
                Password::min(8)->letters()->numbers()
            ],
        ]);

        // Update the standard fields
        $user->full_name = $validatedData['full_name'];
        $user->address = $validatedData['address'];
        $user->country = $validatedData['country'];
        $user->email = $validatedData['email'];

        // Handle the optional password change
        // The filled() method checks if the field is present AND not empty
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }

        // Save the changes to the database
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user' => $user
        ]);
    }

    public function uploadImage(Request $request)
    {
        // Strict Validation: ONLY JPG allowed
        // 'mimes:jpg,jpeg' enforces the exact file extension requirement
        // 'max:2048' limits the file to 2MB to prevent massive uploads crashing your server
        $request->validate([
            'profile_image' => 'required|file|mimes:jpg,jpeg|max:2048'
        ], [
            // This is a custom error message to make it extremely clear to the user
            'profile_image.mimes' => 'Only JPG format is allowed for profile images.'
        ]);

        $user = Auth::user();

        // Handle the file upload
        if ($request->hasFile('profile_image')) {

            // Laravel automatically generates a unique hashed name for the file
            // and stores it in the 'storage/app/public/profiles' folder
            $path = $request->file('profile_image')->store('profiles', 'public');

            // Update the user's database record with the new file path
            $user->profile_image_path = $path;
            $user->save();

            return response()->json([
                'message' => 'Profile image uploaded successfully.',
                // 'asset' generates the full public URL so the frontend can display it
                'image_url' => asset('storage/' . $path)
            ], 200);
        }

        return response()->json(['error' => 'No file was uploaded.'], 400);
    }
}
