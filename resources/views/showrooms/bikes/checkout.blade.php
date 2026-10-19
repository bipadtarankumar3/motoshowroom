@extends('layouts.app')

@section('title', 'Reserve & Book ' . $bike->name . ' — ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50/70 text-slate-900 flex flex-col font-sans selection:bg-rose-500 selection:text-white"
     x-data="{
         paymentMethod: 'razorpay',
         termsAccepted: false,
         termsModalOpen: false,
         sandboxModalOpen: false,
         pendingOrder: null,
         isSubmitting: false,
         errorMessage: '',
         selectedVariant: '{{ $selectedVariant ?? ($bike->variants->first()?->name ?? 'Standard') }}',
         basePrice: {{ (float) $bike->price }},
         rtoEstimate: {{ round($bike->price * 0.13) }},
         insuranceEstimate: {{ round($bike->price * 0.035) }},
         get onRoadPrice() {
             return this.basePrice + this.rtoEstimate + this.insuranceEstimate;
         },
         async submitCheckout(e) {
             const form = document.getElementById('booking-checkout-form');
             if (form && !form.checkValidity()) {
                 form.reportValidity();
                 return;
             }

             if (!this.termsAccepted) {
                 this.termsModalOpen = true;
                 return;
             }

             if (this.paymentMethod === 'offline') {
                 // Offline normal booking: normal form submission
                 form.submit();
                 return;
             }

             // Razorpay online booking: initiate order via AJAX and launch Razorpay modal immediately on this page
             this.isSubmitting = true;
             this.errorMessage = '';

             const formData = new FormData(form);
             formData.append('ajax', '1');

             try {
                 const response = await fetch(form.action, {
                     method: 'POST',
                     headers: {
                         'X-Requested-With': 'XMLHttpRequest',
                         'Accept': 'application/json',
                     },
                     body: formData
                 });

                 const result = await response.json();

                 if (!response.ok || !result.success) {
                     this.isSubmitting = false;
                     this.errorMessage = result.message || (result.errors ? Object.values(result.errors).flat().join(' ') : 'Could not initiate online payment. Please check your form fields.');
                     return;
                 }

                 this.pendingOrder = result;

                 // Launch Razorpay popup right on screen!
                 if (typeof Razorpay !== 'undefined') {
                     const options = {
                         key: result.key_id,
                         amount: result.amount_paisa,
                         currency: result.currency,
                         name: '{{ addslashes($showroom->name) }}',
                         description: 'Reservation Advance #' + result.enquiry_number + ' - ' + result.bike_name,
                         order_id: result.razorpay_order_id,
                         prefill: {
                             name: result.customer_name,
                             email: result.customer_email,
                             contact: result.customer_phone
                         },
                         theme: { color: '#059669' },
                         handler: (rzpResponse) => {
                             this.verifyAndCompletePayment(result, rzpResponse.razorpay_payment_id, rzpResponse.razorpay_signature);
                         },
                         modal: {
                             ondismiss: () => {
                                 this.isSubmitting = false;
                             }
                         }
                     };

                     const rzp = new Razorpay(options);
                     rzp.on('payment.failed', (failedResponse) => {
                         window.location.href = '{{ url('/showrooms/' . $showroom->slug . '/bikes/booking') }}/' + result.enquiry_id + '/failed';
                     });
                     rzp.open();
                 } else {
                     // Fallback if Razorpay SDK script was blocked or sandbox testing
                     this.sandboxModalOpen = true;
                     this.isSubmitting = false;
                 }

             } catch (err) {
                 this.isSubmitting = false;
                 this.errorMessage = 'Network error or gateway unreachable. Please try again.';
                 console.error(err);
             }
         },
         verifyAndCompletePayment(orderData, paymentId, signature) {
             const verifyForm = document.createElement('form');
             verifyForm.method = 'POST';
             verifyForm.action = orderData.verify_url;

             const csrf = document.createElement('input');
             csrf.type = 'hidden';
             csrf.name = '_token';
             csrf.value = '{{ csrf_token() }}';
             verifyForm.appendChild(csrf);

             const paymentIdInput = document.createElement('input');
             paymentIdInput.type = 'hidden';
             paymentIdInput.name = 'payment_id';
             paymentIdInput.value = orderData.payment_id;
             verifyForm.appendChild(paymentIdInput);

             const orderIdInput = document.createElement('input');
             orderIdInput.type = 'hidden';
             orderIdInput.name = 'razorpay_order_id';
             orderIdInput.value = orderData.razorpay_order_id;
             verifyForm.appendChild(orderIdInput);

             const rzpPaymentIdInput = document.createElement('input');
             rzpPaymentIdInput.type = 'hidden';
             rzpPaymentIdInput.name = 'razorpay_payment_id';
             rzpPaymentIdInput.value = paymentId;
             verifyForm.appendChild(rzpPaymentIdInput);

             const signatureInput = document.createElement('input');
             signatureInput.type = 'hidden';
             signatureInput.name = 'razorpay_signature';
             signatureInput.value = signature;
             verifyForm.appendChild(signatureInput);

             document.body.appendChild(verifyForm);
             verifyForm.submit();
         },
         simulatePayment() {
             if (!this.pendingOrder) return;
             this.isSubmitting = true;
             this.verifyAndCompletePayment(
                 this.pendingOrder,
                 this.pendingOrder.simulated_payment_id,
                 this.pendingOrder.simulated_signature
             );
         }
     }">

    @include('showrooms.partials.header')

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">

        {{-- ═══ BREADCRUMBS & TOP FUNNEL ═══ --}}
        <div class="flex items-center justify-between text-xs font-semibold text-slate-500 mb-6 flex-wrap gap-3">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('showrooms.show', $showroom) }}" class="hover:text-slate-900 transition">{{ $showroom->name }}</a>
                <span>/</span>
                <a href="{{ route('showrooms.bikes.index', $showroom) }}" class="hover:text-slate-900 transition">Motorcycles</a>
                <span>/</span>
                <a href="{{ route('showrooms.bikes.show', [$showroom, $bike]) }}" class="hover:text-slate-900 transition">{{ $bike->name }}</a>
                <span>/</span>
                <span class="text-emerald-600 font-bold">Reservation Checkout</span>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    <span>100% Refundable Token</span>
                </span>
            </div>
        </div>

        {{-- Page Heading --}}
        <div class="mb-8">
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    Step 1 of 2: Reservation &amp; Payment Preference
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">
                Complete Vehicle Reservation &amp; Booking
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1">
                Lock your chassis allocation for the <strong>{{ $bike->name }}</strong>. Choose instant Razorpay online token payment or normal offline booking at showroom.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-rose-50 border border-rose-200 p-4 text-rose-800">
                <div class="font-bold text-xs sm:text-sm mb-1.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Please review required details:</span>
                </div>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ═══ 2-COLUMN CHECKOUT GRID ═══ --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            {{-- ═══ LEFT COLUMN: CUSTOMER FORM & PAYMENT METHOD ═══ --}}
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-6">

                {{-- Dynamic Error Banner --}}
                <div x-show="errorMessage" x-cloak class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2 mb-4">
                    <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="errorMessage"></span>
                </div>

                <form method="POST" action="{{ route('showrooms.enquiries.store', $showroom) }}" class="space-y-6" id="booking-checkout-form" @submit.prevent="submitCheckout($event)">
                    @csrf
                    <input type="hidden" name="bike_id" value="{{ $bike->id }}">
                    <input type="hidden" name="type" value="purchase">
                    <input type="hidden" name="booking_amount" value="10000">
                    <input type="hidden" name="payment_method" :value="paymentMethod">

                    {{-- Section 1: Customer Identification --}}
                    <div>
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-800 font-mono text-xs flex items-center justify-center font-bold">1</span>
                            <span>Customer Information</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="customer_name" class="block font-bold text-slate-700 text-xs mb-1">
                                    Full Name <span class="text-rose-600">*</span>
                                </label>
                                <input type="text"
                                       id="customer_name"
                                       name="customer_name"
                                       value="{{ old('customer_name', auth()->user()?->name) }}"
                                       required
                                       placeholder="e.g. Rahul Verma"
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-xs font-medium">
                            </div>

                            <div>
                                <label for="customer_phone" class="block font-bold text-slate-700 text-xs mb-1">
                                    Phone Number <span class="text-rose-600">*</span>
                                </label>
                                <input type="tel"
                                       id="customer_phone"
                                       name="customer_phone"
                                       value="{{ old('customer_phone', auth()->user()?->phone) }}"
                                       required
                                       placeholder="+91 98765 43210"
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-xs font-mono">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="customer_email" class="block font-bold text-slate-700 text-xs mb-1">
                                Email Address <span class="text-rose-600">*</span>
                            </label>
                            <input type="email"
                                   id="customer_email"
                                   name="customer_email"
                                   value="{{ old('customer_email', auth()->user()?->email) }}"
                                   required
                                   placeholder="rahul@example.com"
                                   class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 focus:bg-white focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 text-xs font-medium">
                        </div>
                    </div>

                    {{-- Section 2: Delivery Preferences --}}
                    <div class="pt-4 border-t border-slate-100">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-800 font-mono text-xs flex items-center justify-center font-bold">2</span>
                            <span>Handover &amp; Target Delivery</span>
                        </h2>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="delivery_method" class="block font-bold text-slate-700 text-xs mb-1">Preferred Delivery Method</label>
                                <select id="delivery_method"
                                        name="delivery_method"
                                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs font-medium focus:bg-white cursor-pointer">
                                    <option value="showroom_handover" {{ old('delivery_method') === 'showroom_handover' ? 'selected' : '' }}>
                                        Showroom Pit Handover (Dealership)
                                    </option>
                                    <option value="home_delivery" {{ old('delivery_method') === 'home_delivery' ? 'selected' : '' }}>
                                        Enclosed Flatbed Home Delivery
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label for="preferred_date" class="block font-bold text-slate-700 text-xs mb-1">Target Delivery Date</label>
                                <input type="date"
                                       id="preferred_date"
                                       name="preferred_date"
                                       min="{{ now()->format('Y-m-d') }}"
                                       value="{{ old('preferred_date', now()->addDays(5)->format('Y-m-d')) }}"
                                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs font-medium focus:bg-white">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label for="message" class="block font-bold text-slate-700 text-xs mb-1">Customer Remarks / Customization Notes</label>
                            <textarea id="message"
                                      name="message"
                                      rows="2"
                                      placeholder="e.g. Include tank pad kit, titanium exhaust, or preferred registration plate format."
                                      class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-300 text-slate-900 text-xs font-medium focus:bg-white">{{ old('message') }}</textarea>
                        </div>
                    </div>

                    {{-- Section 3: Booking & Payment Method --}}
                    <div class="pt-4 border-t border-slate-100">
                        <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                            <span class="w-6 h-6 rounded-full bg-slate-100 text-slate-800 font-mono text-xs flex items-center justify-center font-bold">3</span>
                            <span>Choose Payment Method <span class="text-rose-600">*</span></span>
                        </h2>
                        <p class="text-xs text-slate-500 mb-4">
                            Select how you would like to handle the refundable ₹10,000 booking advance token:
                        </p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            {{-- Option A: Razorpay Online Booking --}}
                            <div @click="paymentMethod = 'razorpay'"
                                 :class="paymentMethod === 'razorpay' ? 'border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-600/30 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-xs">Razorpay Online Booking</p>
                                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Instant Priority Lock</span>
                                            </div>
                                        </div>
                                        <span class="w-5 h-5 rounded-full border flex items-center justify-center shrink-0 mt-0.5"
                                              :class="paymentMethod === 'razorpay' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white'">
                                            <svg x-show="paymentMethod === 'razorpay'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 mt-2.5 leading-relaxed">
                                        Pay ₹10,000 refundable advance online via <strong>UPI, Credit/Debit card, or NetBanking</strong>. Instant digital receipt &amp; automated queue jump.
                                    </p>
                                </div>
                            </div>

                            {{-- Option B: Normal Booking (Pay at Showroom) --}}
                            <div @click="paymentMethod = 'offline'"
                                 :class="paymentMethod === 'offline' ? 'border-emerald-600 bg-emerald-50/50 ring-2 ring-emerald-600/30 shadow-sm' : 'border-slate-200 bg-white hover:border-slate-300'"
                                 class="p-4 rounded-2xl border cursor-pointer transition flex flex-col justify-between">
                                <div>
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900 text-xs">Normal Booking</p>
                                                <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-200 text-slate-700">Pay Token at Dealership</span>
                                            </div>
                                        </div>
                                        <span class="w-5 h-5 rounded-full border flex items-center justify-center shrink-0 mt-0.5"
                                              :class="paymentMethod === 'offline' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-300 bg-white'">
                                            <svg x-show="paymentMethod === 'offline'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-600 mt-2.5 leading-relaxed">
                                        Reserve chassis offline. Visit {{ $showroom->name }} within <strong>48 hours</strong> to inspect vehicle and deposit the ₹10,000 token in person.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 4: Terms & Conditions Checkbox --}}
                    <div class="pt-4 border-t border-slate-100">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                            <div class="flex items-start gap-3">
                                <input type="checkbox"
                                       name="terms_accepted"
                                       id="checkout_terms_accepted"
                                       value="1"
                                       x-model="termsAccepted"
                                       required
                                       class="mt-1 h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                                <div class="text-xs text-slate-700 leading-snug">
                                    <label for="checkout_terms_accepted" class="cursor-pointer select-none">
                                        I have reviewed and agree to the
                                    </label>
                                    <button type="button"
                                            @click.prevent.stop="termsModalOpen = true"
                                            id="btn-open-checkout-terms"
                                            class="font-bold text-emerald-600 hover:text-emerald-800 underline underline-offset-2 cursor-pointer transition inline-block">
                                        Vehicle Booking Terms &amp; Reservation Policy
                                    </button>
                                    <span class="text-rose-600 font-bold">*</span>
                                    <span class="block text-[11px] text-slate-500 mt-1">
                                        Covers 100% full refund guarantee, chassis queue locking, and cancellation guidelines.
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 5: Submit Action Button --}}
                    <div>
                        <button type="submit"
                                :disabled="!termsAccepted || isSubmitting"
                                :class="(termsAccepted && !isSubmitting) ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white shadow-lg shadow-emerald-600/25 cursor-pointer hover:scale-[1.01] active:scale-[0.99]' : 'bg-slate-200 text-slate-400 cursor-not-allowed'"
                                class="w-full py-4 rounded-2xl font-black text-xs uppercase tracking-wider transition flex items-center justify-center gap-2">
                            <span x-show="isSubmitting" class="flex items-center gap-2" x-cloak>
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Opening Secure Gateway...</span>
                            </span>
                            <span x-show="!isSubmitting" class="flex items-center gap-2">
                                <span x-text="paymentMethod === 'razorpay' ? 'Proceed to Pay ₹10,000 via Razorpay' : 'Confirm Normal Booking (Pay at Showroom)'"></span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </span>
                        </button>
                        <p x-show="!termsAccepted" class="text-center text-[11px] text-amber-600 font-medium mt-2">
                            * Please review and accept the booking terms to enable reservation.
                        </p>
                    </div>

                </form>

            </div>

            {{-- ═══ RIGHT COLUMN: MOTORCYCLE OVERVIEW & PRICE BREAKDOWN ═══ --}}
            <div class="lg:col-span-5 space-y-5 lg:sticky lg:top-8">

                {{-- Motorcycle Card --}}
                <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm overflow-hidden">
                    <div class="relative rounded-2xl overflow-hidden bg-slate-100 aspect-video mb-4 border border-slate-200 flex items-center justify-center">
                        @if($bike->primaryImage)
                            <img src="{{ Storage::url($bike->primaryImage->image_path) }}"
                                 alt="{{ $bike->name }}"
                                 class="w-full h-full object-cover">
                        @else
                            <div class="text-slate-400 text-center p-4">
                                <svg class="w-12 h-12 mx-auto stroke-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-mono">No Motorcycle Image</span>
                            </div>
                        @endif

                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase bg-white/95 text-slate-800 shadow-xs backdrop-blur-xs">
                            {{ $bike->brand }}
                        </span>
                    </div>

                    <div class="space-y-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Reserved Vehicle</span>
                        <h3 class="text-xl font-black font-display text-slate-900">{{ $bike->name }}</h3>
                        <p class="text-xs text-slate-500 font-mono">SKU: {{ $bike->sku }} • Trim: <span x-text="selectedVariant"></span></p>
                    </div>

                    {{-- Key Specs Pill Grid --}}
                    <div class="grid grid-cols-3 gap-2 py-3.5 my-3.5 border-y border-slate-100 text-center text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Displacement</span>
                            <span class="font-bold text-slate-900">{{ $bike->displacement_cc ? $bike->displacement_cc . ' cc' : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Max Power</span>
                            <span class="font-bold text-slate-900">{{ $bike->power_bhp ? $bike->power_bhp . ' bhp' : 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 uppercase font-semibold block">Mileage</span>
                            <span class="font-bold text-slate-900">{{ $bike->mileage_kmpl ? $bike->mileage_kmpl . ' km/l' : 'N/A' }}</span>
                        </div>
                    </div>

                    {{-- Financial Breakdown --}}
                    <div class="space-y-2 text-xs">
                        <div class="flex justify-between text-slate-600">
                            <span>Ex-Showroom Price:</span>
                            <span class="font-mono font-medium text-slate-900">₹{{ number_format($bike->price) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Estimated RTO &amp; Taxes:</span>
                            <span class="font-mono text-slate-700">₹{{ number_format(round($bike->price * 0.13)) }}</span>
                        </div>
                        <div class="flex justify-between text-slate-600">
                            <span>Comprehensive Insurance:</span>
                            <span class="font-mono text-slate-700">₹{{ number_format(round($bike->price * 0.035)) }}</span>
                        </div>
                        <div class="pt-2 border-t border-slate-100 flex justify-between font-bold text-slate-900">
                            <span>Est. On-Road Price:</span>
                            <span class="font-mono text-slate-900">₹{{ number_format($bike->price + round($bike->price * 0.13) + round($bike->price * 0.035)) }}</span>
                        </div>
                    </div>

                    {{-- Booking Advance Token Highlight --}}
                    <div class="mt-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] uppercase font-bold text-emerald-800 tracking-wider">Payable Today</span>
                            <p class="text-xs text-emerald-700" x-text="paymentMethod === 'razorpay' ? 'Refundable Online Advance' : 'Pay at Showroom in 48 Hours'"></p>
                        </div>
                        <div class="text-right">
                            <span class="text-2xl font-black font-mono text-emerald-600">₹10,000</span>
                        </div>
                    </div>

                    {{-- Refund Guarantee Note --}}
                    <div class="mt-4 flex items-start gap-2.5 text-[11px] text-emerald-800 bg-emerald-50/60 p-3 rounded-xl border border-emerald-100">
                        <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <p class="leading-relaxed">
                            <strong>100% Refundable Booking:</strong> Your ₹10,000 advance is fully refundable anytime prior to tax invoicing &amp; registration.
                        </p>
                    </div>
                </div>

                {{-- Dealership Support Card --}}
                <div class="bg-white rounded-3xl p-5 border border-slate-200 shadow-sm text-xs space-y-2">
                    <p class="font-bold text-slate-900 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        <span>{{ $showroom->name }} Pit Location</span>
                    </p>
                    <p class="text-slate-500 leading-relaxed">{{ $showroom->address }}</p>
                    @if($showroom->phone)
                        <p class="text-slate-700">Direct Desk: <a href="tel:{{ $showroom->phone }}" class="font-bold text-slate-900 hover:text-emerald-600">{{ $showroom->phone }}</a></p>
                    @endif
                </div>

            </div>

        </div>

    </main>

    {{-- ═══ TERMS & CONDITIONS POPUP MODAL ═══ --}}
    <div x-cloak x-show="termsModalOpen"
         class="fixed inset-0 z-70 overflow-y-auto"
         style="z-index: 70;"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="termsModalOpen = false"></div>
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-xl w-full p-6 sm:p-8 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                 @click.away="termsModalOpen = false">

                {{-- Header --}}
                <div class="flex items-start justify-between pb-4 border-b border-slate-100">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase tracking-wider">
                            Legal &amp; Dealership Guidelines
                        </span>
                        <h3 class="text-xl font-black font-display text-slate-900 mt-1">Booking Terms &amp; Reservation Policy</h3>
                        <p class="text-xs text-slate-500">{{ $showroom->name }} • Authorized Customer Agreement</p>
                    </div>
                    <button type="button" @click="termsModalOpen = false" class="p-2 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-xl transition cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Terms Body --}}
                <div class="py-4 space-y-4 max-h-[60vh] overflow-y-auto pr-2 text-xs text-slate-700 leading-relaxed">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 font-mono text-[11px] font-bold inline-flex items-center justify-center">1</span>
                            Token Advance &amp; Chassis Allocation
                        </h4>
                        <p class="text-[11px] text-slate-600 pl-6.5">
                            The booking token amount of <strong>₹10,000</strong> secures your vehicle queue priority for <strong>{{ $bike->name }}</strong>. Submitting a reservation blocks the vehicle VIN/chassis against your contact details.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-emerald-50/60 border border-emerald-200 space-y-1">
                        <h4 class="font-bold text-emerald-950 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-600 text-white font-mono text-[11px] font-bold inline-flex items-center justify-center">2</span>
                            100% Full Refund Guarantee
                        </h4>
                        <p class="text-[11px] text-emerald-900 pl-6.5">
                            Your reservation advance is <strong>100% refundable</strong> with zero cancellation charges prior to final commercial invoicing and RTO tax registration. If you change your mind for any reason, the advance will be credited back via original payment channel within 3–5 working days.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 font-mono text-[11px] font-bold inline-flex items-center justify-center">3</span>
                            Normal Booking (Pay at Showroom)
                        </h4>
                        <p class="text-[11px] text-slate-600 pl-6.5">
                            If choosing Normal Booking, your provisional allocation is held for <strong>48 hours</strong>. You must visit {{ $showroom->name }} with valid ID/Address proofs to inspect the motorcycle and deposit the booking advance in person; unconfirmed bookings may be released thereafter.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 font-mono text-[11px] font-bold inline-flex items-center justify-center">4</span>
                            Razorpay Online Booking
                        </h4>
                        <p class="text-[11px] text-slate-600 pl-6.5">
                            Online transactions are encrypted with 256-bit SSL and processed via Razorpay. Supported modes include UPI (GPay, PhonePe, Paytm), Credit &amp; Debit Cards, NetBanking, and authorized wallets. A digital receipt and tracking ID will be generated immediately upon authorization.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                        <h4 class="font-bold text-slate-900 text-xs flex items-center gap-1.5">
                            <span class="w-5 h-5 rounded-full bg-slate-200 text-slate-800 font-mono text-[11px] font-bold inline-flex items-center justify-center">5</span>
                            On-Road Pricing &amp; Delivery Settlement
                        </h4>
                        <p class="text-[11px] text-slate-600 pl-6.5">
                            The ₹10,000 token is adjusted against your final invoice. The final on-road amount is subject to state RTO road tax rates, mandatory insurance coverages, and any manufacturer ex-showroom price revisions applicable on the invoice generation date.
                        </p>
                    </div>
                </div>

                {{-- Modal Action Footer --}}
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-end gap-3">
                    <button type="button"
                            @click="termsModalOpen = false"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition cursor-pointer">
                        Close
                    </button>
                    <button type="button"
                            @click="termsAccepted = true; termsModalOpen = false"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-sm cursor-pointer flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        <span>I Read &amp; Accept Booking Terms</span>
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ═══ SANDBOX SIMULATION MODAL (FOR TEST MODE / OFFLINE SDK FALLBACK) ═══ --}}
    <div x-cloak x-show="sandboxModalOpen"
         class="fixed inset-0 z-70 overflow-y-auto"
         style="z-index: 70;"
         role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="sandboxModalOpen = false"></div>
        <div class="min-h-screen px-4 text-center flex items-center justify-center p-4">
            <div class="relative bg-white rounded-3xl max-w-md w-full p-6 text-left shadow-2xl border border-slate-200 my-8 overflow-hidden"
                 @click.away="sandboxModalOpen = false">
                <div class="flex items-start justify-between pb-3 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                            ⚡
                        </span>
                        <div>
                            <h3 class="text-base font-black font-display text-slate-900">Sandbox Payment Gateway</h3>
                            <p class="text-[11px] text-slate-500">Test Simulator (No Live Card Charged)</p>
                        </div>
                    </div>
                    <button type="button" @click="sandboxModalOpen = false" class="p-1.5 text-slate-400 hover:text-slate-700 rounded-lg cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="py-4 space-y-3 text-xs text-slate-600">
                    <p class="leading-relaxed">
                        Reservation Order <strong>#<span x-text="pendingOrder?.enquiry_number"></span></strong> is ready for verification.
                    </p>
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 space-y-1">
                        <div class="flex justify-between text-slate-500">
                            <span>Amount:</span>
                            <span class="font-bold text-slate-900 font-mono">₹10,000.00</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Order ID:</span>
                            <span class="font-mono text-[11px] text-slate-700 truncate max-w-[200px]" x-text="pendingOrder?.razorpay_order_id"></span>
                        </div>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <button type="button"
                            @click="simulatePayment()"
                            :disabled="isSubmitting"
                            class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                        <span x-show="isSubmitting">Authorizing...</span>
                        <span x-show="!isSubmitting">⚡ Complete Simulated Payment</span>
                    </button>
                    <button type="button"
                            @click="sandboxModalOpen = false"
                            class="w-full py-2.5 px-4 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-semibold text-xs transition cursor-pointer">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    @include('showrooms.partials.footer')
</div>

{{-- Razorpay Checkout Client SDK --}}
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endsection
