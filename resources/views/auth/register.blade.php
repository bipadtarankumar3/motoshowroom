<x-guest-layout title="Create Your Account - MotoShowroom SaaS">
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
                <h2 class="mt-4 text-3xl font-black font-display text-slate-900 tracking-tight">Create an Account</h2>
                <p class="mt-2 text-sm text-slate-500">Join MotoShowroom SaaS to manage your bike showroom</p>
            </div>

            <!-- Registration Card -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-200 relative overflow-hidden">
                <form method="POST" action="{{ route('register') }}" class="space-y-5" id="form-register">
                    @csrf

                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required autofocus
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('name') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="e.g. John Doe">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('email') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="john@example.com">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone & City Row -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('phone') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                                   placeholder="+91 9876543210">
                            @error('phone')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="city" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">City</label>
                            <input type="text" name="city" id="city" value="{{ old('city') }}"
                                   class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('city') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                                   placeholder="Mumbai, Bangalore...">
                            @error('city')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border @error('password') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="Minimum 8 characters">
                        @error('password')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                               class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 text-sm transition"
                               placeholder="Re-enter your password">
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="btn-submit-register"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-md shadow-rose-600/25 hover:shadow-lg hover:shadow-rose-600/40 hover:scale-[1.01] transition duration-200">
                        Create Account & View Profile
                    </button>
                </form>

                <!-- Google OAuth Divider -->
                <div class="mt-6 relative flex items-center justify-center">
                    <div class="border-t border-slate-200 w-full"></div>
                    <span class="bg-white px-3 text-[11px] uppercase tracking-wider text-slate-400 font-bold absolute">Or register with</span>
                </div>

                <!-- Google Register Button -->
                <div class="mt-6">
                    <a href="{{ route('auth.google') }}" id="btn-google-register"
                       class="w-full py-3 px-4 rounded-xl bg-white hover:bg-slate-50 text-slate-700 font-semibold text-sm border border-slate-300 flex items-center justify-center gap-3 transition shadow-xs">
                        <svg class="w-5 h-5" viewBox="0 0 24 24">
                            <path fill="#EA4335" d="M12 5c1.6 0 3 .6 4.1 1.7l3.1-3.1C17.3 1.8 14.8 1 12 1 7.5 1 3.7 3.6 1.9 7.4l3.7 2.9C6.5 7.4 9 5 12 5z"/>
                            <path fill="#4285F4" d="M23.5 12.3c0-.8-.1-1.7-.2-2.3H12v4.6h6.5c-.3 1.5-1.1 2.8-2.4 3.7l3.7 2.9c2.2-2 3.7-5 3.7-8.9z"/>
                            <path fill="#FBBC05" d="M5.6 14.7c-.2-.7-.4-1.5-.4-2.7s.2-2 .4-2.7L1.9 6.4C.7 8.8 0 10.8 0 12s.7 3.2 1.9 5.6l3.7-2.9z"/>
                            <path fill="#34A853" d="M12 23c3.2 0 6-1.1 8-3l-3.7-2.9c-1.1.7-2.5 1.2-4.3 1.2-3 0-5.5-2-6.4-4.8L1.9 16.4C3.7 20.2 7.5 23 12 23z"/>
                        </svg>
                        <span>Sign up with Google</span>
                    </a>
                </div>

                <div class="mt-6 text-center text-sm text-slate-500 border-t border-slate-200 pt-5">
                    Already registered?
                    <a href="{{ route('login') }}" class="font-semibold text-rose-600 hover:text-rose-700 ml-1">Sign in here</a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
