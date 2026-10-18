<x-app-layout :title="'Checkout - ' . $plan->name . ' - ' . $showroom->name">
<div class="min-h-screen bg-slate-950 text-slate-100 py-10 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-8">

        <!-- Back to Billing Navigation -->
        <div class="flex items-center justify-between text-xs text-slate-400">
            <a href="{{ route('showrooms.billing.index', $showroom) }}" class="inline-flex items-center gap-1.5 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span>Back to Billing & Plans</span>
            </a>
            <span class="inline-flex items-center gap-1.5 text-emerald-400 font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>256-Bit SSL Encrypted Checkout</span>
            </span>
        </div>

        <!-- Header Card -->
        <div class="glass-panel-dark rounded-3xl p-6 sm:p-8 border border-slate-800 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-16 -top-16 w-64 h-64 bg-rose-600/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
                <div class="space-y-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                            SaaS Plan Subscription
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            Razorpay Verified
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">
                        Complete Plan Purchase
                    </h1>
                    <p class="text-xs text-slate-400">
                        Showroom: <span class="text-white font-semibold">{{ $showroom->name }}</span> • Billed {{ ucfirst($interval) }}
                    </p>
                </div>

                <div class="text-right">
                    <span class="text-xs text-slate-400">Total Payable Amount</span>
                    <p class="text-3xl font-black font-display text-emerald-400">
                        ₹{{ number_format($payment->amount) }}
                    </p>
                    <span class="text-[10px] text-slate-500 font-mono">{{ $payment->currency }} (Includes all applicable taxes)</span>
                </div>
            </div>
        </div>

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Order Summary & Checkout -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Plan Entitlements Summary -->
            <div class="glass-panel-dark rounded-3xl p-6 border border-slate-800 shadow-xl space-y-4">
                <h3 class="text-xs font-bold text-white uppercase tracking-wider">Plan Summary</h3>
                <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-white text-base">{{ $plan->name }} Tier</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/20 text-rose-300 uppercase">{{ $interval }}</span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">{{ $plan->description }}</p>
                    <div class="pt-2 border-t border-slate-800 text-xs space-y-1.5 text-slate-300">
                        <div class="flex justify-between">
                            <span class="text-slate-400">Motorcycle Listings:</span>
                            <span class="font-bold text-white">{{ $plan->max_bike_listings }} active listings</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Staff Accounts:</span>
                            <span class="font-bold text-white">{{ $plan->max_staff_accounts }} accounts</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Monthly Bookings:</span>
                            <span class="font-bold text-white">{{ $plan->max_monthly_bookings }} bookings</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">360° Motorcycle Viewer:</span>
                            <span class="{{ $plan->has_360_viewer ? 'text-emerald-400 font-bold' : 'text-slate-500' }}">
                                {{ $plan->has_360_viewer ? 'Included' : 'Not included' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-slate-400 space-y-1 font-mono">
                    <p>Order Reference: <span class="text-slate-200">{{ $payment->razorpay_order_id }}</span></p>
                    <p>Transaction ID: <span class="text-slate-200">#PAY-{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</span></p>
                </div>
            </div>

            <!-- Payment Action & Simulator -->
            <div class="glass-panel-dark rounded-3xl p-6 border border-slate-800 shadow-xl space-y-6 flex flex-col justify-between">
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-white uppercase tracking-wider">Payment Options</h3>

                    <!-- Razorpay Live Checkout Button -->
                    <button type="button" id="btn-rzp-pay"
                            class="w-full py-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-bold text-sm shadow-xl shadow-blue-600/30 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14H9v-2h2v2zm0-4H9V7h2v5z"/></svg>
                        <span>Pay ₹{{ number_format($payment->amount) }} via Razorpay</span>
                    </button>

                    <div class="relative flex items-center justify-center py-2">
                        <div class="border-t border-slate-800 w-full"></div>
                        <span class="bg-slate-950 px-3 text-[10px] text-slate-500 uppercase tracking-wider absolute">or Sandbox / Development Simulation</span>
                    </div>

                    <!-- Sandbox Instant Simulation Form -->
                    <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-400">Development Sandbox Simulator</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/10 text-emerald-300">HMAC-SHA256</span>
                        </div>
                        <p class="text-[11px] text-slate-400 leading-relaxed">
                            Simulates payment capture with valid cryptographic HMAC-SHA256 server verification without requiring active banking credentials.
                        </p>

                        <!-- Hidden Signature Form -->
                        <form method="POST" action="{{ route('showrooms.payments.verify', $showroom) }}" id="payment-verify-form">
                            @csrf
                            <input type="hidden" name="payment_id" value="{{ $payment->id }}">
                            <input type="hidden" name="razorpay_order_id" value="{{ $payment->razorpay_order_id }}">
                            <input type="hidden" name="razorpay_payment_id" value="{{ $simulatedPaymentId }}" id="rzp_payment_id">
                            <input type="hidden" name="razorpay_signature" value="{{ $simulatedSignature }}" id="rzp_signature">
                            <input type="hidden" name="method" value="upi">

                            <button type="submit" id="btn-simulate-payment"
                                    class="w-full py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs transition shadow-md shadow-emerald-600/20 flex items-center justify-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span>Simulate Successful Payment</span>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800/80 text-[10px] text-slate-500 flex items-center justify-between">
                    <span>Razorpay Key: <span class="font-mono text-slate-400">{{ substr($keyId, 0, 10) }}...</span></span>
                    <span>Order: <span class="font-mono text-slate-400">{{ $payment->razorpay_order_id }}</span></span>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Razorpay Checkout Script Integration -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
document.getElementById('btn-rzp-pay')?.addEventListener('click', function(e) {
    e.preventDefault();

    const options = {
        key: "{{ $keyId }}",
        amount: {{ (int) round($payment->amount * 100) }},
        currency: "{{ $payment->currency }}",
        name: "MotoShowroom SaaS",
        description: "{{ $plan->name }} Subscription ({{ ucfirst($interval) }})",
        order_id: "{{ $payment->razorpay_order_id }}",
        prefill: {
            name: "{{ auth()->user()?->name }}",
            email: "{{ auth()->user()?->email }}",
            contact: "{{ auth()->user()?->phone ?? '+919999999999' }}"
        },
        theme: {
            color: "#e11d48"
        },
        handler: function(response) {
            document.getElementById('rzp_payment_id').value = response.razorpay_payment_id;
            document.getElementById('rzp_signature').value = response.razorpay_signature;
            document.getElementById('payment-verify-form').submit();
        }
    };

    if (window.Razorpay) {
        const rzp = new Razorpay(options);
        rzp.open();
    } else {
        alert('Razorpay Checkout SDK is offline. Please use the simulated sandbox button below.');
    }
});
</script>
</x-app-layout>
