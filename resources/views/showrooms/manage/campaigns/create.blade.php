@extends('layouts.app')

@section('title', 'Create Promotional Campaign - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col">
    @include('showrooms.partials.manage-nav')

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs font-medium text-slate-500 mb-2">
            <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Dashboard</a>
            <span>/</span>
            <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}" class="hover:text-slate-900 transition">Campaigns</a>
            <span>/</span>
            <span class="text-slate-700 font-semibold">New Campaign</span>
        </div>

        <div class="mb-8">
            <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">Create Promotional Campaign</h1>
            <p class="text-sm text-slate-500 mt-1">Configure digital scratch cards, set probability weights, and allocate prize quotas.</p>
        </div>

        <div class="rounded-3xl bg-white border border-slate-200 p-6 sm:p-8 shadow-xs">
            <form method="POST" action="{{ route('showrooms.manage.campaigns.store', $showroom) }}" class="space-y-8">
                @csrf

                <!-- Campaign Core Info -->
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                        <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                        1. Campaign Overview
                    </h3>

                    <div>
                        <label for="name" class="block text-xs font-semibold text-slate-700 mb-1">
                            Campaign Name <span class="text-purple-600">*</span>
                        </label>
                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               placeholder="e.g. Diwali Superbike Festival Scratch & Win"
                               class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        @error('name')
                            <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-xs font-semibold text-slate-700 mb-1">Marketing Tagline / Description</label>
                        <textarea id="description"
                                  name="description"
                                  rows="2"
                                  placeholder="Describe the occasion, eligible motorcycle models, or showroom benefits..."
                                  class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                        <div>
                            <label for="start_date" class="block text-xs font-semibold text-slate-700 mb-1">Start Date <span class="text-purple-600">*</span></label>
                            <input type="date"
                                   id="start_date"
                                   name="start_date"
                                   value="{{ old('start_date', date('Y-m-d')) }}"
                                   required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            @error('start_date')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="end_date" class="block text-xs font-semibold text-slate-700 mb-1">End Date <span class="text-purple-600">*</span></label>
                            <input type="date"
                                   id="end_date"
                                   name="end_date"
                                   value="{{ old('end_date', date('Y-m-d', strtotime('+30 days'))) }}"
                                   required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            @error('end_date')
                                <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block text-xs font-semibold text-slate-700 mb-1">Initial Status <span class="text-purple-600">*</span></label>
                            <select id="status" name="status" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                <option value="active" @selected(old('status', 'active') === 'active')>Active & Live</option>
                                <option value="draft" @selected(old('status') === 'draft')>Draft (Hidden)</option>
                                <option value="paused" @selected(old('status') === 'paused')>Paused</option>
                            </select>
                        </div>

                        <div>
                            <label for="applicable_to" class="block text-xs font-semibold text-slate-700 mb-1">Target Audience <span class="text-purple-600">*</span></label>
                            <select id="applicable_to" name="applicable_to" class="w-full bg-white border border-slate-300 rounded-xl px-3 py-2.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                <option value="all" @selected(old('applicable_to', 'all') === 'all')>All Bookings & Visitors</option>
                                <option value="service" @selected(old('applicable_to') === 'service')>Service & Repair Bookings</option>
                                <option value="bike" @selected(old('applicable_to') === 'bike')>Bike Purchase & Test Rides</option>
                            </select>
                        </div>

                        <div>
                            <label for="max_participations_per_customer" class="block text-xs font-semibold text-slate-700 mb-1">Max Cards / Rider <span class="text-purple-600">*</span></label>
                            <input type="number"
                                   id="max_participations_per_customer"
                                   name="max_participations_per_customer"
                                   value="{{ old('max_participations_per_customer', 1) }}"
                                   min="1"
                                   max="20"
                                   required
                                   class="w-full bg-white border border-slate-300 rounded-xl px-4 py-2.5 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                        </div>
                    </div>
                </div>

                <!-- Prize Pool Configuration -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-200 pb-2">
                        <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            2. Prize Tiers & Probabilities
                        </h3>
                        <span class="text-xs text-slate-500">Total available quantity is enforced server-side with zero overselling.</span>
                    </div>

                    @error('prizes')
                        <p class="text-[11px] text-rose-600">{{ $message }}</p>
                    @enderror

                    <div id="prize-rows-container" class="space-y-3">
                        <!-- Prize Tier 1: Free Accessory -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-4">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Prize Title</label>
                                <input type="text" name="prizes[0][title]" value="Free Premium Riding Gloves" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Type</label>
                                <select name="prizes[0][prize_type]" class="w-full bg-white border border-slate-300 rounded-xl px-2 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                    @foreach($prizeTypes as $pt)
                                        <option value="{{ $pt->value }}" @selected($pt->value === 'free_accessory')>{{ $pt->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Quantity</label>
                                <input type="number" name="prizes[0][total_quantity]" value="10" min="0" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Weight</label>
                                <input type="number" name="prizes[0][probability_weight]" value="15" min="1" max="100" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-1 text-center pt-4">
                                <span class="text-xs text-slate-400 font-bold">&bull;</span>
                            </div>
                        </div>

                        <!-- Prize Tier 2: Service Discount -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-4">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Prize Title</label>
                                <input type="text" name="prizes[1][title]" value="Flat ₹500 Off Workshop General Service" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Type</label>
                                <select name="prizes[1][prize_type]" class="w-full bg-white border border-slate-300 rounded-xl px-2 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                    @foreach($prizeTypes as $pt)
                                        <option value="{{ $pt->value }}" @selected($pt->value === 'service_discount')>{{ $pt->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Quantity</label>
                                <input type="number" name="prizes[1][total_quantity]" value="50" min="0" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Weight</label>
                                <input type="number" name="prizes[1][probability_weight]" value="35" min="1" max="100" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-1 text-center pt-4">
                                <span class="text-xs text-slate-400 font-bold">&bull;</span>
                            </div>
                        </div>

                        <!-- Prize Tier 3: Better Luck Next Time -->
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200 grid grid-cols-1 sm:grid-cols-12 gap-3 items-center">
                            <div class="sm:col-span-4">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Prize Title</label>
                                <input type="text" name="prizes[2][title]" value="Better Luck Next Time" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-3">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Type</label>
                                <select name="prizes[2][prize_type]" class="w-full bg-white border border-slate-300 rounded-xl px-2 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                                    @foreach($prizeTypes as $pt)
                                        <option value="{{ $pt->value }}" @selected($pt->value === 'better_luck')>{{ $pt->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Quantity</label>
                                <input type="number" name="prizes[2][total_quantity]" value="999" min="0" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] uppercase font-bold text-slate-500 mb-1">Weight</label>
                                <input type="number" name="prizes[2][probability_weight]" value="50" min="1" max="100" required class="w-full bg-white border border-slate-300 rounded-xl px-3 py-1.5 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">
                            </div>
                            <div class="sm:col-span-1 text-center pt-4">
                                <span class="text-xs text-slate-400 font-bold">&bull;</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-slate-900 flex items-center gap-2 border-b border-slate-200 pb-2">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        3. Terms, Restrictions & Rules
                    </h3>

                    <div>
                        <label for="terms_and_conditions" class="block text-xs font-semibold text-slate-700 mb-1">Terms & Conditions</label>
                        <textarea id="terms_and_conditions"
                                  name="terms_and_conditions"
                                  rows="3"
                                  placeholder="e.g. Valid only for redemption in physical showroom workshop. Cannot be exchanged for cash. One coupon per invoice."
                                  class="w-full bg-white border border-slate-300 rounded-xl p-3 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500/20 focus:border-purple-500">{{ old('terms_and_conditions') }}</textarea>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="pt-6 border-t border-slate-200 flex items-center justify-end gap-3">
                    <a href="{{ route('showrooms.manage.campaigns.index', $showroom) }}"
                       class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 border border-slate-300 text-slate-700 text-xs font-bold transition">
                        Cancel
                    </a>
                    <button type="submit"
                            id="btn-save-campaign"
                            class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-bold text-xs uppercase tracking-wider shadow-md shadow-purple-600/20 transition">
                        Publish Campaign
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>
@endsection
