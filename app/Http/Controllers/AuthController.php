<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        // 1. Validate the incoming request
        // The required rule ensures the field is not null or empty
        $validatedData = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => [
                'required', 'string',
                Password::min(8)
                ->letters()
                ->numbers()
            ],
        ]);

        // 2. Create the User
        // We use Hash::make() to encrypt the password before it goes into the database.
        $user = User::create([
            'full_name' => $validatedData['full_name'],
            'address'   => $validatedData['address'],
            'country'   => $validatedData['country'],
            'email'     => $validatedData['email'],
            'password'  => Hash::make($validatedData['password']),
        ]);

        // 3. Return a Response
        // Since we are building the backend logic, returning a JSON response
        // is the easiest way to test if it worked.
        return response()->json([
            'message' => 'User registered successfully!',
            'user' => $user
        ], 201);
    }
}
