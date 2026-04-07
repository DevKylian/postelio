<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Exception;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Erreur lors de la connexion avec Google.');
        }

        // Find or create user
        $user = User::where('google_id', $googleUser->id)->first();

        if (!$user) {
            // Check if user exists with the same email
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser) {
                // Link Google account to existing user
                $existingUser->update([
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'avatar' => $googleUser->avatar,
                ]);

                if (!$existingUser->hasVerifiedEmail()) {
                    $existingUser->markEmailAsVerified();
                }

                $user = $existingUser;
            } else {
                // Create a new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                    'avatar' => $googleUser->avatar,
                    'password' => Hash::make(Str::random(24)), // Random password for security
                    'newsletter_opt_in' => session('register_newsletter_opt_in', true),
                ]);

                session()->forget('register_newsletter_opt_in');

                $user->markEmailAsVerified();

                // Create automatic company for new user (Postelio logic)
                Company::create([
                    'user_id' => $user->id,
                ]);

                Auth::login($user);

                return redirect()->intended(route('onboarding', absolute: false));
            }
        } else {
            // Update tokens
            $user->update([
                'google_token' => $googleUser->token,
                'google_refresh_token' => $googleUser->refreshToken,
                'avatar' => $googleUser->avatar,
            ]);
        }

        Auth::login($user);

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
