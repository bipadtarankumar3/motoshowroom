<x-guest-layout title="Sign In - MotoShowroom SaaS">
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
                <h2 class="mt-4 text-3xl font-black font-display text-slate-900 tracking-tight">Sign In</h2>
                <p class="mt-2 text-sm text-slate-500">Access your showroom administration or profile</p>
            </div>

            <!-- Login Card -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-200 relative overflow-hidden">
                <form method="POST" action="{{ route('login') }}" class="space-y-5" id="form-login">
                    @csrf

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('email') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="your.email@example.com">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">Password</label>
                            <a href="{{ route('password.request') }}" class="text-xs text-rose-600 hover:text-rose-700 font-medium">Forgot password?</a>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('password') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="Enter your password">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me Checkbox -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer text-slate-600">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-rose-600 focus:ring-rose-500">
                            <span class="text-xs">Remember my login</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit-login"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-md shadow-rose-600/25 hover:shadow-lg hover:shadow-rose-600/40 hover:scale-[1.01] transition duration-200">
                        Sign In to Your Account
                    </button>
                </form>

                <!-- Google OAuth Divider -->
                <div class="mt-6 relative flex items-center justify-center">
                    <div class="border-t border-slate-200 w-full"></div>
                    <span class="bg-white px-3 text-[11px] uppercase tracking-wider text-slate-400 font-bold absolute">Or continue with</span>
                </div>

                <!-- Google Login Button -->
                <div class="mt-6">
                    <a href="{{ route('auth.google') }}" id="btn-google-login"
                       class="w-full py-3 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm border border-slate-300 flex items-center justify-center gap-3 transition shadow-xs">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.4l3.7 2.9C6.5 7.4 9 5 12 5z"/>
                            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/>
                            <path fill="#FBBC05" d="M5.6 14.7c-.2-.7-.4-1.5-.4-2.7s.2-2 .4-2.7L1.9 6.4C.7 8.8 0 10.8 0 12s.7 3.2 1.9 5.6l3.7-2.9z"/>
                            <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2-6.4-4.8L1.9 16.4C3.7 20.2 7.5 23 12 23z"/>
                        </svg>
                        <span>Sign in with Google</span>
                    </a>
                </div>

                <!-- Quick Seed Credentials Helper -->
                <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200 text-xs text-slate-600">
                    <p class="font-bold text-slate-700 uppercase tracking-wider text-[10px] mb-2">Default Demo Credentials:</p>
                    <div class="flex flex-col gap-1 font-mono text-[11px]">
                        <div class="flex justify-between items-center">
                            <span>Admin: <strong class="text-rose-600">admin@motoshowroom.example</strong></span>
                            <span class="text-slate-500">Password123!</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span>User: <strong class="text-sky-600">user@motoshowroom.example</strong></span>
                            <span class="text-slate-500">Password123!</span>
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center text-sm text-slate-500 border-t border-slate-200 pt-5">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="font-semibold text-rose-600 hover:text-rose-700 ml-1">Register now</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
