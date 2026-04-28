<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Mail\VerificationCodeMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;


class LoginController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        // 2. Check if a user exists and is already blocked
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($user->is_blocked) {
            return response()->json(['error' => 'You are blocked, contact the administrative support.'], 403);
        }

        // 3. Check the password
        if (!Hash::check($request->password, $user->password)) {
            // Password is WRONG: Increment the fails
            $user->failed_login_attempts += 1;
            $user->save();

            // 4. Trigger the Email on exactly 3 fails
            if ($user->failed_login_attempts == 3) {

                // Generate a 6 digit random code
                $code = rand(100000, 999999);

                // Save it to the database so you can check it on the 4th attempt
                $user->verification_code = $code;
                $user->save();

                // SEND THE EMAIL
                Mail::to($user->email)->send(new VerificationCodeMail($code));

                return response()->json([
                    'message' => '3 failed attempts. A verification code has been sent to your email.',
                    'require_verification' => true
                ], 401);
            }

            // Standard wrong password response (attempts 1 and 2)
            return response()->json([
                'error' => 'Invalid credentials. Attempt ' . $user->failed_login_attempts . ' of 3.'
            ], 401);
        }

        // 5. Password is CORRECT: Reset attempts and log them in
        $user->failed_login_attempts = 0;
        $user->verification_code = null;
        $user->save();

        // Create a token or session here for the successful login
        // $token = $user->createToken('auth-token')->plainTextToken;
        // Log the user into the Laravel session
        Auth::login($user);

        return response()->json([
            'message' => 'Login successful!',
            //'token' => $token
        ]);
    }

    public function verifyLogin(Request $request)
    {
        // 1. Validate that they sent an email and a code
        $request->validate([
            'email' => 'required|email',
            'code'  => 'required|numeric'
        ]);

        $user = User::where('email', $request->email)->first();

        // 2. Standard checks
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        if ($user->is_blocked) {
            return response()->json([
                'error' => 'You are blocked, contact the administrative support.'
            ], 403);
        }

        // 3. THE 4TH STRIKE: Did they enter the wrong code?
        if ($request->code != $user->verification_code) {

            // Lock the account!
            $user->is_blocked = true;
            // Clear the code so it can't be guessed later
            $user->verification_code = null;
            $user->save();

            // Return the exact blocked message your assignment requires
            return response()->json([
                'error' => 'You are blocked, contact the administrative support.'
            ], 403);
        }

        // 4. SUCCESS: The code was correct!
        // Reset everything back to normal
        $user->failed_login_attempts = 0;
        $user->verification_code = null;
        $user->save();

        // Log the user in
        Auth::login($user);

        return response()->json([
            'message' => 'Verification successful! You are now logged in.',
            'user' => $user
        ], 200);
    }
}
