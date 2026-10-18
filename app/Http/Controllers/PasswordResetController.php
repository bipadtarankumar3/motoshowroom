<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    /**
     * Show the request password reset link form.
     */
    public function showForgotPassword(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Generate password reset token and store in password_reset_tokens table.
     */
    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            // Return back with generic message for privacy/security
            return back()->with('status', 'If an account exists with that email, a password reset link has been dispatched.');
        }

        $token = Str::random(60);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now(),
            ]
        );

        // Store active test token in session in local/testing environment for easy verification
        if (app()->environment('local', 'testing')) {
            session()->flash('demo_reset_url', route('password.reset', ['token' => $token, 'email' => $user->email]));
        }

        return back()->with('status', 'Password reset instructions have been dispatched.');
    }

    /**
     * Display the password reset form with token and email.
     */
    public function showResetPassword(Request $request, string $token): View
    {
        $email = $request->query('email');

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Reset the user's password using the submitted token.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return back()->withErrors(['email' => 'This password reset token is invalid or has expired.']);
        }

        // Check if token expired (60 minutes)
        if (now()->diffInMinutes($record->created_at) > 60) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();

            return back()->withErrors(['email' => 'This password reset token has expired. Please request a new one.']);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'Unable to locate user account.']);
        }

        $user->update([
            'password' => $request->password,
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Your password has been reset successfully. You may now sign in.');
    }
}
