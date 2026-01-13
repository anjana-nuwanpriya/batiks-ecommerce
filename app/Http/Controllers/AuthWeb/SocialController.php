<?php

namespace App\Http\Controllers\AuthWeb;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class SocialController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $socialUser = Socialite::driver('google')->stateless()->user();
            return $this->loginOrCreateUser($socialUser, 'google');
        } catch (Exception $e) {
            Log::error('Google login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Google login failed. Please try again.');
        }
    }

    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $socialUser = Socialite::driver('facebook')->stateless()->user();
            return $this->loginOrCreateUser($socialUser, 'facebook');
        } catch (Exception $e) {
            Log::error('Facebook login failed: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Facebook login failed. Please try again.');
        }
    }

    private function loginOrCreateUser($socialUser, $provider)
    {
        $providerId = $socialUser->getId();
        $providerEmail = $socialUser->getEmail();
        $providerName = $socialUser->getName();

        $user = User::where('provider_id', $providerId)
            ->where('provider', $provider)
            ->first();

        if (!$user) {
            $user = User::where('email', $providerEmail)->first();

            if ($user) {
                $user->update([
                    'provider' => $provider,
                    'provider_id' => $providerId,
                ]);
            } else {
                $user = User::create([
                    'name' => $providerName ?? 'Unknown',
                    'email' => $providerEmail ?? uniqid('user_') . '@noemail.com',
                    'password' => Hash::make('12345678'), // Replace with secure random password
                    'provider' => $provider,
                    'is_active' => true,
                    'provider_id' => $providerId,
                ]);
            }
        }



        // Check if the user is banned/inactive
        if (!$user->is_active) {
            toast()->error('Your account is banned. Please contact the admin.');
            return redirect()->route('user.login');
        }
        // Log in the user
        Auth::login($user, true);
        toast()->success('Login successful');

        return redirect()->intended(route('home'));
    }

}
