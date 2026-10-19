@extends('layouts.app')

@section('title', 'Storefront Temporarily Offline — ' . $showroom->name)

@section('content')
<div class="min-h-[85vh] bg-slate-50 flex flex-col justify-center items-center px-4 sm:px-6 lg:px-8 py-16">
    <div class="w-full max-w-xl mx-auto text-center space-y-8">

        {{-- Showroom Emblem / Brand Icon --}}
        <div class="inline-flex items-center justify-center">
            @if($showroom->logo_url)
                <img src="{{ $showroom->logo_url }}" alt="{{ $showroom->name }}"
                     class="h-16 w-auto max-w-[200px] rounded-2xl object-contain drop-shadow-sm">
            @else
                <div class="w-20 h-20 rounded-3xl flex items-center justify-center font-black text-white text-2xl font-display shadow-lg shadow-slate-900/10 relative overflow-hidden"
                     style="background: linear-gradient(135deg, {{ $showroom->brand_color_primary ?? '#dc2626' }}, {{ $showroom->brand_color_accent ?? '#991b1b' }})">
                    <div class="absolute -right-3 -bottom-3 w-12 h-12 rounded-full bg-white/10 blur-xs"></div>
                    <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.5 8c-1.38 0-2.5 1.12-2.5 2.5 0 .28.05.55.13.8L14.7 13H10l-1.3-2.6A3.49 3.49 0 005.5 9C3.57 9 2 10.57 2 12.5S3.57 16 5.5 16c1.61 0 2.96-1.09 3.37-2.57L11.5 15h4.15l-1.15 2.3a2.5 2.5 0 102.24 1.12L18.4 15.1c.35.13.72.2 1.1.2 1.93 0 3.5-1.57 3.5-3.5S21.43 8 19.5 8zm-14 6c-.83 0-1.5-.67-1.5-1.5S4.67 11 5.5 11s1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm14-1.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                    </svg>
                </div>
            @endif
        </div>

        {{-- Offline Status Card --}}
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm space-y-6">
            <div class="space-y-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                    <span>Storefront Currently Offline</span>
                </span>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-slate-900 tracking-tight">
                    {{ $showroom->name }}
                </h1>
                <p class="text-sm sm:text-base text-slate-600 leading-relaxed max-w-md mx-auto">
                    The online storefront for this dealership is temporarily offline or undergoing scheduled maintenance by the dealership management team.
                </p>
            </div>

            {{-- Contact Information / Direct Inquiries --}}
            @if($showroom->phone || $showroom->email || $showroom->city)
                <div class="pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-3 text-left">
                    @if($showroom->phone)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Direct Telephone</span>
                            <a href="tel:{{ $showroom->phone }}" class="text-xs font-bold text-slate-900 hover:text-rose-600 transition block mt-0.5">
                                {{ $showroom->phone }}
                            </a>
                        </div>
                    @endif

                    @if($showroom->email)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Email Inquiries</span>
                            <a href="mailto:{{ $showroom->email }}" class="text-xs font-bold text-slate-900 hover:text-rose-600 transition block mt-0.5 truncate">
                                {{ $showroom->email }}
                            </a>
                        </div>
                    @endif

                    @if($showroom->city)
                        <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 sm:col-span-2">
                            <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Physical Showroom Location</span>
                            <p class="text-xs font-semibold text-slate-800 mt-0.5">
                                {{ $showroom->city }}{{ $showroom->state ? ', ' . $showroom->state : '' }}
                            </p>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Management Back-Office / Login Actions --}}
            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-3">
                @auth
                    @if(auth()->user()->isAdmin() || auth()->user()->canAccessTenant($showroom))
                        <a href="{{ route('showrooms.manage.website', $showroom) }}"
                           class="w-full sm:w-auto px-6 py-3 rounded-xl text-white font-bold text-xs uppercase tracking-wider shadow-sm transition hover:brightness-110"
                           style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                            Enable Storefront in Builder →
                        </a>
                        <a href="{{ route('showrooms.manage', $showroom) }}"
                           class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition">
                            Back-Office
                        </a>
                    @else
                        <a href="{{ url('/') }}"
                           class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition">
                            Back to Platform Home
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}"
                       class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs uppercase tracking-wider transition">
                        Staff &amp; Owner Login
                    </a>
                    <a href="{{ url('/') }}"
                       class="w-full sm:w-auto px-6 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs uppercase tracking-wider transition">
                        Platform Home
                    </a>
                @endauth
            </div>
        </div>

        {{-- Footer note --}}
        <p class="text-xs text-slate-400">
            &copy; {{ date('Y') }} {{ $showroom->name }}. Powered by MotoShowroom Platform.
        </p>
    </div>
</div>
@endsection
