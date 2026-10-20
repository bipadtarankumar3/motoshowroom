@extends('layouts.app')

@section('title', 'Edit ' . $user->name . ' — Showroom Owner Profile')

@section('content')
<div class="min-h-screen bg-slate-50 text-slate-900 flex flex-col font-sans">

    @if($showroom)
        @include('showrooms.partials.manage-nav')
    @endif

    <main class="flex-1 w-full px-4 sm:px-6 lg:px-8 xl:px-10 py-6 sm:py-8 space-y-6">

        <!-- Breadcrumbs -->
        <div class="flex items-center gap-2 text-xs text-slate-500">
            @if($showroom)
                <a href="{{ route('showrooms.manage', $showroom) }}" class="hover:text-slate-900 transition">Showroom Back-Office</a>
                <span>/</span>
                <a href="{{ route('showrooms.manage.profile', $showroom) }}" class="hover:text-slate-900 transition">Owner Profile</a>
            @else
                <a href="{{ route('dashboard') }}" class="hover:text-slate-900 transition">Dashboard</a>
                <span>/</span>
                <a href="{{ route('profile') }}" class="hover:text-slate-900 transition">My Profile</a>
            @endif
            <span>/</span>
            <span class="text-brand-primary font-bold">Edit Details</span>
        </div>

        <!-- Edit Form Card -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-xs relative overflow-hidden max-w-4xl">
            <div class="border-b border-slate-100 pb-5 mb-6">
                <h1 class="text-2xl font-black font-display text-slate-900">Edit Showroom Owner Profile</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Update your personal contact details, location, and account credentials.</p>
            </div>

            @php
                $formAction = ($showroom && $showroom->exists)
                    ? route('showrooms.manage.profile.update', $showroom)
                    : route('users.update', $user);
                $cancelUrl = ($showroom && $showroom->exists)
                    ? route('showrooms.manage.profile', $showroom)
                    : route('users.show', $user);
            @endphp

            <form method="POST" action="{{ $formAction }}" class="space-y-6" id="form-edit-owner-profile">
                @csrf
                @method('PUT')

                <!-- Name & Email Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <!-- Full Name -->
                    <div>
                        <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Full Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border @error('name') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary text-sm transition">
                        @error('name')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div>
                        <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Official Email Address <span class="text-rose-500">*</span>
                        </label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border @error('email') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary text-sm transition">
                        @error('email')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Phone & City -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border @error('phone') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary text-sm transition"
                               placeholder="+91 9876543210">
                        @error('phone')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="city" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">City / Region</label>
                        <input type="text" name="city" id="city" value="{{ old('city', $user->city) }}"
                               class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border @error('city') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary text-sm transition"
                               placeholder="e.g. Bangalore, Mumbai...">
                        @error('city')
                            <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Address -->
                <div>
                    <label for="address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Physical Address / Headquarters Location</label>
                    <input type="text" name="address" id="address" value="{{ old('address', $user->address) }}"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border @error('address') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary text-sm transition"
                           placeholder="Street address, showroom suite, or corporate headquarters">
                    @error('address')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio / Notes -->
                <div>
                    <label for="bio" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">Bio / Dealership Notes</label>
                    <textarea name="bio" id="bio" rows="3"
                              class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border @error('bio') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary text-sm transition leading-relaxed"
                              placeholder="Brief description about yourself or your showroom background...">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Change Password (Optional) -->
                <div class="pt-4 border-t border-slate-100">
                    <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                        New Password <span class="text-slate-400 font-normal lowercase">(leave blank to keep current)</span>
                    </label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border @error('password') border-rose-500 @else border-slate-300 @enderror text-slate-900 placeholder-slate-400 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-primary/20 focus:border-brand-primary text-sm transition"
                           placeholder="Enter a new password if you wish to change it (minimum 8 characters)">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Action Buttons -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <a href="{{ $cancelUrl }}" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition border border-slate-300 shadow-2xs">
                        Cancel
                    </a>
                    <button type="submit" id="btn-save-owner-details"
                            class="btn-brand-primary px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-wider shadow-md transition cursor-pointer">
                        Save Profile Changes
                    </button>
                </div>
            </form>
        </div>

    </main>
</div>
@endsection
