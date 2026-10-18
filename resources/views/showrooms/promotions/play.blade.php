@extends('layouts.app')

@section('title', 'Scratch & Win - ' . $showroom->name)

@section('content')
<div class="min-h-screen bg-slate-950 text-slate-100 flex flex-col font-sans selection:bg-purple-500 selection:text-white">

    <!-- Header -->
    <header class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('showrooms.show', $showroom) }}" class="flex items-center gap-3">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center font-black text-white text-xs shadow"
                          style="background-color: {{ $showroom->brand_color_primary ?? '#dc2626' }}">
                        {{ strtoupper(substr($showroom->name, 0, 1)) }}
                    </span>
                    <div>
                        <span class="font-display font-black text-sm text-white block leading-none">{{ $showroom->name }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">{{ $campaign->name }}</span>
                    </div>
                </a>

                <div class="flex items-center gap-3">
                    <a href="{{ route('showrooms.promotions.index', $showroom) }}" class="text-xs text-slate-400 hover:text-white transition">
                        &larr; More Promotions
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="flex-1 flex items-center justify-center p-4 sm:p-8">

        <div class="w-full max-w-lg mx-auto">

            <!-- Card Header -->
            <div class="text-center mb-6">
                <span class="text-xs font-bold uppercase tracking-widest text-purple-400 block mb-1">Official Showroom Voucher</span>
                <h1 class="text-2xl sm:text-3xl font-black font-display text-white tracking-tight">Your Digital Scratch Card</h1>
                <p class="text-xs text-slate-400 mt-1">Scratch the silver foil below with your finger or mouse to reveal your reward!</p>
            </div>

            <!-- The Scratch Card Container -->
            <div class="relative rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900 to-purple-950/40 border-2 border-purple-500/30 p-6 sm:p-8 shadow-2xl overflow-hidden backdrop-blur">
                <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-purple-500/10 blur-3xl pointer-events-none"></div>

                <!-- Recipient Identity -->
                <div class="flex items-center justify-between text-xs text-slate-400 border-b border-slate-800 pb-3 mb-6">
                    <div>
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Issued For</span>
                        <strong class="text-white">{{ $participation->customer_name }}</strong>
                    </div>
                    <div class="text-right">
                        <span class="text-slate-500 block text-[10px] uppercase font-bold">Card Token</span>
                        <span class="font-mono text-purple-400 font-bold">#{{ substr($participation->participation_token, 0, 8) }}</span>
                    </div>
                </div>

                <!-- Interactive Scratch Stage -->
                <div class="relative w-full aspect-[16/10] rounded-2xl overflow-hidden bg-slate-950 border border-slate-800 shadow-inner flex items-center justify-center p-4">

                    <!-- UNDERNEATH: The Hidden Prize / Outcome -->
                    <div id="prize-container" class="w-full text-center space-y-3 z-0">
                        @if($participation->prize && $participation->prize->prize_type->isWinningPrize())
                            <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white flex items-center justify-center mx-auto shadow-xl shadow-emerald-900/40 animate-bounce">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            </div>
                            <span class="text-xs px-3 py-1 rounded-lg font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 uppercase tracking-wider inline-block">
                                {{ $participation->prize->prize_type->label() }}
                            </span>
                            <h3 class="text-xl sm:text-2xl font-black font-display text-white" id="prize-title">
                                {{ $participation->prize->title }}
                            </h3>
                        @else
                            <div class="w-16 h-16 rounded-2xl bg-slate-800 text-slate-400 flex items-center justify-center mx-auto shadow">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <h3 class="text-xl font-bold font-display text-white">Better Luck Next Time!</h3>
                            <p class="text-xs text-slate-400">Thank you for visiting {{ $showroom->name }}. Keep riding!</p>
                        @endif

                        <!-- Coupon Code Display (If won) -->
                        @if($participation->coupon_code)
                            <div class="pt-2">
                                <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Coupon Claim Code</span>
                                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-900 border border-purple-500/50 shadow">
                                    <span class="font-mono text-base font-black text-purple-400 tracking-wider" id="coupon-code-text">{{ $participation->coupon_code }}</span>
                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $participation->coupon_code }}'); alert('Coupon code copied to clipboard!');" class="text-slate-400 hover:text-white transition" title="Copy Coupon Code">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- ON TOP: HTML5 Scratch Canvas (Only if not already scratched) -->
                    @if(! $participation->is_scratched)
                        <canvas id="scratch-canvas"
                                class="absolute inset-0 w-full h-full cursor-pointer z-10 touch-none select-none"
                                width="450"
                                height="280"></canvas>
                    @endif
                </div>

                <!-- Footer & Action Buttons -->
                <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
                    @if(! $participation->is_scratched)
                        <span class="text-slate-400 text-center sm:text-left">Swipe to scratch or click button:</span>
                        <button type="button"
                                id="btn-instant-reveal"
                                class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold transition shadow-lg shadow-purple-600/30">
                            Instant Reveal Prize
                        </button>
                    @else
                        <div class="w-full text-center p-3 rounded-xl bg-slate-950/80 border border-slate-800 text-slate-300">
                            Scratched on <strong class="text-white">{{ $participation->scratched_at?->format('M d, Y h:i A') }}</strong>. Present this code at our showroom.
                        </div>
                    @endif
                </div>

            </div>

        </div>

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-6 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} {{ $showroom->name }}. Powered by MotoShowroom SaaS.</p>
    </footer>

</div>

<!-- Interactive Scratch Canvas Script -->
@if(! $participation->is_scratched)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('scratch-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let isDrawing = false;
    let isRevealed = false;

    // Fill canvas with metallic silver scratch coating
    function initCanvas() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = rect.height;

        const gradient = ctx.createLinearGradient(0, 0, canvas.width, canvas.height);
        gradient.addColorStop(0, '#94a3b8');
        gradient.addColorStop(0.5, '#cbd5e1');
        gradient.addColorStop(1, '#64748b');

        ctx.fillStyle = gradient;
        ctx.fillRect(0, 0, canvas.width, canvas.height);

        // Pattern text on foil
        ctx.fillStyle = '#334155';
        ctx.font = 'bold 15px sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('✨ SCRATCH HERE TO REVEAL ✨', canvas.width / 2, canvas.height / 2);
    }

    initCanvas();
    window.addEventListener('resize', initCanvas);

    function scratch(x, y) {
        ctx.globalCompositeOperation = 'destination-out';
        ctx.beginPath();
        ctx.arc(x, y, 25, 0, Math.PI * 2);
        ctx.fill();
    }

    function getCoords(e) {
        const rect = canvas.getBoundingClientRect();
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: clientX - rect.left,
            y: clientY - rect.top
        };
    }

    // Touch and mouse events
    function startDraw(e) {
        isDrawing = true;
        const pos = getCoords(e);
        scratch(pos.x, pos.y);
    }

    let checkThrottle = 0;
    function moveDraw(e) {
        if (!isDrawing) return;
        const pos = getCoords(e);
        scratch(pos.x, pos.y);

        checkThrottle++;
        if (checkThrottle % 15 === 0) {
            checkPercentScratched();
        }
    }

    function endDraw() {
        isDrawing = false;
        checkPercentScratched();
    }

    canvas.addEventListener('mousedown', startDraw);
    canvas.addEventListener('mousemove', moveDraw);
    window.addEventListener('mouseup', endDraw);

    canvas.addEventListener('touchstart', startDraw, { passive: true });
    canvas.addEventListener('touchmove', moveDraw, { passive: true });
    window.addEventListener('touchend', endDraw);

    function checkPercentScratched() {
        if (isRevealed) return;

        try {
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const data = imageData.data;
            let transparentPixels = 0;
            const totalPixels = data.length / 4;

            for (let i = 3; i < data.length; i += 16) {
                if (data[i] === 0) transparentPixels++;
            }

            const percent = (transparentPixels / (totalPixels / 4)) * 100;
            if (percent > 35) {
                revealCard();
            }
        } catch (e) {
            // Ignore cross-origin canvas security if any
        }
    }

    function revealCard() {
        if (isRevealed) return;
        isRevealed = true;

        canvas.style.transition = 'opacity 0.6s ease';
        canvas.style.opacity = '0';
        setTimeout(() => canvas.remove(), 600);

        // Notify server
        fetch("{{ route('showrooms.scratch.reveal', [$showroom, $participation]) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        });
    }

    const instantBtn = document.getElementById('btn-instant-reveal');
    if (instantBtn) {
        instantBtn.addEventListener('click', function () {
            revealCard();
            instantBtn.remove();
        });
    }
});
</script>
@endif
@endsection
