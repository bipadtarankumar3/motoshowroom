<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\OtpService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Show the user registration form.
     */
    public function showRegister(): View
    {
        return view('auth.register');
    }

    /**
     * Handle public user registration.
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => 'user',
            'phone' => $validated['phone'] ?? null,
            'city' => $validated['city'] ?? null,
            'status' => 'active',
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('users.show', $user)->with('success', 'Registration successful! Welcome to your personal dashboard.');
    }

    /**
     * Show the login form.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Handle user login credentials and challenge with OTP.
     */
    public function login(LoginRequest $request, OtpService $otpService): RedirectResponse
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        // Validate user credentials without completing login session yet
        if (! Auth::validate($credentials)) {
            return back()->withInput($request->only('email', 'remember'))->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->status === 'suspended') {
            return redirect()->route('login')->withErrors([
                'email' => 'Your account is suspended. Please contact platform support.',
            ]);
        }

        // Generate OTP (defaults to 123456 via OtpService)
        $otp = $otpService->generateOtp($user);

        // Store pending authentication state in session
        $request->session()->put('auth.otp', [
            'user_id' => $user->id,
            'email' => $user->email,
            'remember' => $remember,
            'code' => $otp,
            'generated_at' => now()->timestamp,
        ]);

        return redirect()->route('login.otp')->with('status', 'A 6-digit verification code has been sent (Default: 123456).');
    }

    /**
     * Show the OTP verification form.
     */
    public function showOtp(Request $request): View|RedirectResponse
    {
        $pending = $request->session()->get('auth.otp');
        if (! $pending) {
            return redirect()->route('login');
        }

        return view('auth.otp-verify', [
            'email' => $pending['email'],
        ]);
    }

    /**
     * Verify the entered OTP and finalize the user login.
     */
    public function verifyOtp(Request $request, OtpService $otpService): RedirectResponse
    {
        $pending = $request->session()->get('auth.otp');
        if (! $pending) {
            return redirect()->route('login')->withErrors([
                'email' => 'Session expired. Please sign in again.',
            ]);
        }

        $request->validate([
            'otp' => ['required', 'string', 'digits:6'],
        ]);

        $enteredOtp = (string) $request->input('otp');
        $expectedOtp = (string) ($pending['code'] ?? '');

        if (! $otpService->verifyOtp($enteredOtp, $expectedOtp)) {
            return back()->withErrors([
                'otp' => 'Invalid OTP code. Please enter 123456.',
            ]);
        }

        $user = User::find($pending['user_id']);
        if (! $user) {
            $request->session()->forget('auth.otp');

            return redirect()->route('login');
        }

        // Clear OTP session state and log the user in
        $request->session()->forget('auth.otp');
        Auth::login($user, (bool) ($pending['remember'] ?? false));
        $request->session()->regenerate();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('success', "Welcome back, Admin {$user->name}!");
        }

        $showroom = $user->showrooms()->first();
        if ($showroom) {
            return redirect()->intended(route('showrooms.manage', $showroom))->with('success', "Welcome back, {$user->name}!");
        }

        return redirect()->intended(route('users.show', $user))->with('success', "Welcome back, {$user->name}!");
    }

    /**
     * Resend/regenerate OTP.
     */
    public function resendOtp(Request $request, OtpService $otpService): RedirectResponse
    {
        $pending = $request->session()->get('auth.otp');
        if (! $pending) {
            return redirect()->route('login');
        }

        $user = User::find($pending['user_id']);
        $newOtp = $otpService->generateOtp($user);

        $pending['code'] = $newOtp;
        $pending['generated_at'] = now()->timestamp;
        $request->session()->put('auth.otp', $pending);

        return back()->with('status', 'A new verification code has been dispatched (Default: 123456).');
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'You have been logged out successfully.');
    }
}
