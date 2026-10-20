<x-guest-layout title="Verify Two-Factor OTP — MotoShowroom SaaS">
    <div class="min-h-screen flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
        <div class="w-full max-w-md">
            <!-- Brand Logo Header -->
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-rose-600 to-amber-500 p-0.5 shadow-md shadow-rose-600/25 group-hover:scale-105 transition">
                        <div class="w-full h-full bg-white rounded-[14px] flex items-center justify-center">
                            <svg class="w-7 h-7 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                    </div>
                </a>
                <h2 class="mt-4 text-3xl font-black font-display text-slate-900 tracking-tight">Enter Security OTP</h2>
                <p class="mt-2 text-sm text-slate-500">
                    A 6-digit verification code has been generated for
                    <span class="font-bold text-slate-800">{{ $email }}</span>
                </p>
            </div>

            <!-- Elevated Card -->
            <div class="bg-white rounded-3xl p-8 shadow-xl border border-slate-200 relative overflow-hidden">
                <!-- Hint / Banner -->
                <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center gap-2.5">
                    <span class="text-base">🔐</span>
                    <div>
                        <span class="font-bold">Test Mode Active:</span> Default OTP is <strong class="font-mono text-sm bg-white px-2 py-0.5 rounded border border-amber-300 text-amber-950 font-black">123456</strong>
                    </div>
                </div>

                @if (session('status'))
                    <div class="mb-5 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-semibold">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.otp.verify') }}" class="space-y-5" id="form-otp-verify">
                    @csrf

                    <!-- 6 Digit OTP input -->
                    <div>
                        <label for="otp" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 mb-1.5 text-center">
                            6-Digit Verification Code
                        </label>
                        <input type="text"
                               name="otp"
                               id="otp"
                               maxlength="6"
                               pattern="\d{6}"
                               inputmode="numeric"
                               autocomplete="one-time-code"
                               required
                               autofocus
                               placeholder="123456"
                               class="w-full text-center text-2xl tracking-[0.5em] font-mono py-3 px-4 rounded-xl bg-slate-50 border @error('otp') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-rose-500 font-bold transition">
                        @error('otp')
                            <p class="mt-2 text-xs text-rose-600 font-medium text-center">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            id="btn-verify-otp"
                            class="w-full py-3.5 px-4 rounded-xl bg-gradient-to-r from-rose-600 to-rose-500 hover:from-rose-500 hover:to-rose-400 text-white font-bold text-sm shadow-md shadow-rose-600/25 hover:shadow-lg hover:shadow-rose-600/40 hover:scale-[1.01] transition duration-200 flex items-center justify-center gap-2">
                        <span>Verify & Complete Login</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </form>

                <!-- Actions: Resend or Cancel -->
                <div class="mt-6 pt-5 border-t border-slate-100 flex items-center justify-between text-xs">
                    <form method="POST" action="{{ route('login.otp.resend') }}">
                        @csrf
                        <button type="submit" class="text-rose-600 hover:text-rose-700 font-semibold hover:underline">
                            Resend Code
                        </button>
                    </form>

                    <a href="{{ route('login') }}" class="text-slate-500 hover:text-slate-800 font-semibold">
                        ← Back to Sign In
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
