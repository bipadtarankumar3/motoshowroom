<?php

namespace App\Http\Controllers;

use App\Models\Showroom;
use App\Models\ShowroomMembership;
use App\Models\SocialAccount;
use App\Models\SubscriptionPlan;
use App\Models\User;
use App\Services\SubscriptionService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    /**
     * Redirect the user to Google OAuth authentication page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and authenticate.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Google authentication failed or was cancelled. Please try again.');
        }

        // 1. Check if social account already linked
        $socialAccount = SocialAccount::where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->first();

        if ($socialAccount) {
            $user = $socialAccount->user;

            if ($user->status === 'suspended') {
                return redirect()->route('login')->with('error', 'Your account is suspended. Please contact support.');
            }

            Auth::login($user);
            request()->session()->regenerate();

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', "Signed in with Google as {$user->name}.");
            }

            $showroom = $user->showrooms()->first();
            if ($showroom) {
                return redirect()->intended(route('showrooms.manage', $showroom))->with('success', "Signed in with Google as {$user->name}.");
            }

            return redirect()->intended(route('profile'))->with('success', "Signed in with Google as {$user->name}.");
        }

        // 2. Check if a user with this email already exists
        $user = User::where('email', $googleUser->getEmail())->first();
        $isNewUser = false;

        if (! $user) {
            $isNewUser = true;
            // Register new user: strictly assign role 'user' (never admin)
            $user = User::create([
                'name' => $googleUser->getName() ?: 'Google User',
                'email' => $googleUser->getEmail(),
                'password' => Str::random(24),
                'role' => 'user',
                'status' => 'active',
                'avatar_url' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'onboarding_completed_at' => null, // Explicitly trigger modern onboarding modal
            ]);
        }

        // 3. Link Google social account
        SocialAccount::create([
            'user_id' => $user->id,
            'provider' => 'google',
            'provider_id' => $googleUser->getId(),
            'token' => $googleUser->token ?? null,
            'refresh_token' => $googleUser->refreshToken ?? null,
            'expires_at' => isset($googleUser->expiresIn) ? now()->addSeconds((int) $googleUser->expiresIn) : null,
        ]);

        Auth::login($user);
        request()->session()->regenerate();

        // 4. Ensure new user has an initial Showroom Dealership and 14-day evaluation trial
        $showroom = $user->showrooms()->first();

        if (! $showroom) {
            $baseName = $user->name ? trim($user->name) : 'Dealer';
            $showroomName = $baseName.' Motors';
            $baseSlug = Str::slug($showroomName);
            $slug = $baseSlug;
            $counter = 1;
            while (Showroom::where('slug', $slug)->exists()) {
                $slug = $baseSlug.'-'.$counter++;
            }

            $showroom = Showroom::create([
                'name' => $showroomName,
                'slug' => $slug,
                'email' => $user->email,
                'phone' => '+91 98000 00000',
                'address_line1' => 'Showroom Address to be configured',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'postal_code' => '560001',
                'country' => 'India',
                'brand_color_primary' => '#DC2626',
                'brand_color_accent' => '#0F172A',
                'status' => 'trial',
                'public_website_enabled' => true,
                'settings' => [
                    'tagline' => 'Premium Multi-Brand Motorcycle Showroom',
                    'about_text' => 'Welcome to '.$showroomName.'. Authorized dealer for premium motorcycles and riding accessories.',
                ],
            ]);

            ShowroomMembership::create([
                'showroom_id' => $showroom->id,
                'user_id' => $user->id,
                'role' => 'owner',
                'status' => 'active',
            ]);

            // Provision initial 14-Day evaluation trial
            $defaultPlan = SubscriptionPlan::first();
            if ($defaultPlan) {
                app(SubscriptionService::class)->startTrial($showroom, $defaultPlan);
            }
        }

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'))->with('success', "Signed in with Google as {$user->name}.");
        }

        return redirect()->route('showrooms.manage', $showroom)
            ->with('success', 'Signed in with Google successfully! Welcome to your showroom dashboard.');
    }
}
