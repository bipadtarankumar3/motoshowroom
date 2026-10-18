<x-guest-layout title="Forgot Password - MotoShowroom SaaS">
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
                <h2 class="mt-4 text-3xl font-black font-display text-slate-900 tracking-tight">Reset Password</h2>
                <p class="mt-2 text-sm text-slate-500">Enter your email and we'll dispatch a secure reset link</p>
            </div>

            <!-- Card -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-200 relative overflow-hidden">
                @if (session('status'))
                    <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                        {{ session('status') }}
                    </div>
                @endif

                @if (session('demo_reset_url'))
                    <div class="mb-5 p-4 rounded-xl bg-sky-50 border border-sky-200 text-sky-800 text-xs">
                        <span class="font-bold block mb-1 uppercase tracking-wider text-[10px]">Testing Quick-Link:</span>
                        <a href="{{ session('demo_reset_url') }}" class="underline text-sky-600 break-all font-mono">
                            Click here to reset password directly
                        </a>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5" id="form-forgot-password">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Account Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('email') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="your.email@example.com">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit-forgot-password"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-md shadow-rose-600/25 hover:shadow-lg hover:shadow-rose-600/40 hover:scale-[1.01] transition duration-200">
                        Dispatch Reset Link
                    </button>
                </form>

                <div class="mt-6 text-center text-sm text-slate-500 border-t border-slate-200 pt-5">
                    Remember your password?
                    <a href="{{ route('login') }}" class="font-semibold text-rose-600 hover:text-rose-700 ml-1">Sign in here</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
