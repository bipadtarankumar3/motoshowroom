<x-guest-layout title="Sign In - MotoShowroom SaaS">
    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full max-w-md">
            <!-- Brand Logo Header -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 p-0.5 shadow-xl shadow-rose-600/30 group-hover:scale-105 transition">
                        <div class="w-full h-full bg-slate-950 rounded-[14px] flex items-center justify-center">
                            <svg class="w-7 h-7 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                    </div>
                </a>
                <h2 class="mt-4 text-3xl font-black font-display text-white tracking-tight">Sign In</h2>
                <p class="mt-2 text-sm text-slate-400">Access your showroom administration or profile</p>
            </div>

            <!-- Login Card -->
            <div class="glass-panel-dark rounded-3xl p-8 shadow-2xl border border-slate-800 backdrop-blur-xl relative overflow-hidden">
                <div class="absolute -top-24 -right-24 w-48 h-48 bg-rose-600/20 blur-3xl rounded-full pointer-events-none"></div>

                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="form-login">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('email') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="your.email@example.com">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">Password</label>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-900/80 border @error('password') border-rose-500 @else border-slate-700/80 @enderror text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="Enter your password">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-300">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-900 border-slate-700 text-rose-600 focus:ring-rose-500">
                            <span class="text-xs">Remember my login</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit-login"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-xl shadow-rose-600/30 hover:shadow-rose-600/50 hover:scale-[1.01] transition duration-200">
                        Sign In to Your Account
                    </button>
                </form>

                <!-- Quick Seed Credentials Helper (For convenient testing) -->
                <div class="mt-6 p-4 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs text-slate-400">
                    <p class="font-bold text-slate-300 uppercase tracking-wider text-[10px] mb-2">Default Demo Credentials:</p>
                    <div class="flex flex-col gap-1 font-mono text-[11px]">
                        <div class="flex justify-between items-center">
                            <span>Admin: <strong class="text-rose-400">admin@motoshowroom.example</strong></span>
                            <span class="text-slate-500">Password123!</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>User: <strong class="text-sky-400">user@motoshowroom.example</strong></span>
                            <span class="text-slate-500">Password123!</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center text-sm text-slate-400 border-t border-slate-800 pt-5">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-rose-400 hover:text-rose-300 ml-1">Register now</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
