<x-guest-layout title="Set New Password - MotoShowroom SaaS">
    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
        <div class="w-full max-w-md">
            <!-- Brand Logo Header -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 p-0.5 shadow-md shadow-rose-600/25 group-hover:scale-105 transition">
                        <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center">
                            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </a>
                <h2 class="mt-4 text-3xl font-black font-display text-slate-900 tracking-tight">Create New Password</h2>
                <p class="mt-2 text-sm text-slate-500">Choose a strong password for your account</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-200 relative overflow-hidden">
                <form method="POST" action="{{ route('password.update') }}" class="space-y-5" id="form-reset-password">
                    @csrf

                    <!-- Token hidden field -->
                    <input type="hidden" name="token" value="{{ $token }}">

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $email) }}" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('email') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="your.email@example.com">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- New Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">New Password</label>
                        <input type="password" name="password" id="password" required autofocus
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('password') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="Minimum 8 characters">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="Re-enter new password">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit-reset-password"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-md shadow-rose-600/25 hover:shadow-lg hover:shadow-rose-600/40 hover:scale-[1.01] transition duration-200">
                        Update Password & Sign In
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-slate-500 border-t border-slate-200 pt-5">
                    Remember your password?
                    <a href="{{ route('login') }}" class="font-semibold text-rose-600 hover:text-rose-700 ml-1">Sign in</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
