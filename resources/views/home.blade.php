@extends('layouts.app')

@section('title', 'Rajdoot Nivedan Media - Live Music Distribution & Earnings')

@section('content')
<div class="container-fluid px-4 px-lg-5 py-4 py-lg-5">

    <!-- =========================================================================
         1. HERO SECTION (Exact match to Image 1)
         ========================================================================= -->
    <div class="row align-items-center g-5 mb-5 pb-lg-5">
        
        <!-- Left: Copy & CTAs -->
        <div class="col-lg-6">
            
            <!-- Category Badge -->
            <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-4" style="background: rgba(0, 210, 170, 0.08); border: 1px solid rgba(0, 210, 170, 0.35);">
                <i class="fa-solid fa-wand-magic-sparkles text-teal" style="font-size: 0.85rem;"></i>
                <span class="small fw-semibold text-teal">Distribution &middot; Streaming &middot; Earnings</span>
            </div>

            <!-- Title -->
            <h1 class="display-3 fw-extrabold text-light mb-2" style="letter-spacing: -1.5px; line-height: 1.1;">
                Rajdoot Nivedan<br>
                <span class="text-teal">Media</span>
            </h1>

            <!-- Subtitle (Bengali + English) -->
            <p class="text-white fs-5 mb-4" style="line-height: 1.6; max-width: 540px; color: #ffffff !important;">
                বিশ্বজুড়ে আপনার সঙ্গীত ছড়িয়ে দিন সহজেই ও দ্রুত। We deliver your music to Spotify, Apple Music, JioSaavn, Instagram &amp; more &mdash; and pay you live.
            </p>

            <!-- CTA Buttons -->
            <div class="d-flex flex-wrap align-items-center gap-3 mb-5">
                <a href="{{ route('register') }}" class="btn-teal fs-6 py-3 px-4">
                    <span>Start earning</span>
                    <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>

                <a href="{{ route('login') }}" class="btn-dark-outline fs-6 py-3 px-4">
                    <i class="fa-solid fa-play me-2" style="font-size: 0.75rem;"></i>
                    <span>Customer login</span>
                </a>
            </div>

            <!-- Platform Store Pills Row -->
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="px-3 py-1.5 rounded-pill small fw-semibold text-light" style="background: #111827; border: 1px solid var(--border-subtle);">
                    <i class="fa-brands fa-spotify text-success me-1"></i> Spotify
                </span>
                <span class="px-3 py-1.5 rounded-pill small fw-semibold text-light" style="background: #111827; border: 1px solid var(--border-subtle);">
                    <i class="fa-brands fa-apple text-danger me-1"></i> Apple Music
                </span>
                <span class="px-3 py-1.5 rounded-pill small fw-semibold text-light" style="background: #111827; border: 1px solid var(--border-subtle);">
                    <i class="fa-solid fa-music text-success me-1"></i> JioSaavn
                </span>
                <span class="px-3 py-1.5 rounded-pill small fw-semibold text-light" style="background: #111827; border: 1px solid var(--border-subtle);">
                    <i class="fa-brands fa-instagram text-danger me-1"></i> Instagram
                </span>
                <span class="px-3 py-1.5 rounded-pill small fw-semibold text-light" style="background: #111827; border: 1px solid var(--border-subtle);">
                    <i class="fa-brands fa-facebook text-primary me-1"></i> Facebook
                </span>
                <span class="px-3 py-1.5 rounded-pill small fw-semibold text-light" style="background: #111827; border: 1px solid var(--border-subtle);">
                    <i class="fa-brands fa-youtube text-danger me-1"></i> YouTube
                </span>
            </div>

        </div>

        <!-- Right: Live Lifetime Earnings Card (Exact match to Image 1) -->
        <div class="col-lg-6">
            <div class="p-4 p-md-5 rounded-4 position-relative" style="background: #0d1526; border: 1px solid rgba(255, 255, 255, 0.08); box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6); overflow: hidden;">
                
                <!-- Floating White Logo Pill/Square in top right -->
                <div class="position-absolute" style="top: 24px; right: 24px;">
                    <div style="width: 44px; height: 44px; background: #ffffff; border-radius: 12px; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="width: 32px; height: 32px; object-fit: contain;">
                    </div>
                </div>

                <!-- Earnings Title & Status -->
                <div class="mb-3">
                    <div class="text-uppercase small fw-bold text-white" style="letter-spacing: 1px; font-size: 0.72rem; color: #ffffff !important;">
                        LIFETIME EARNINGS
                    </div>
                    <div class="d-flex align-items-center gap-3 mt-1">
                        <div class="fs-2 fw-extrabold text-light" style="font-size: 2.2rem !important; letter-spacing: -0.5px;">
                            ₹ 4,58,850<span class="text-teal">.00</span>
                        </div>
                        <span class="badge px-2.5 py-1 rounded-pill small fw-bold" style="background: rgba(0, 210, 170, 0.15); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.35);">
                            <span class="pulsing-dot me-1"></span> LIVE
                        </span>
                    </div>
                </div>

                <!-- Animated / Glowing Line Graph Canvas -->
                <div class="my-3 position-relative" style="height: 180px;">
                    <canvas id="heroChart" class="w-100 h-100"></canvas>
                </div>

                <!-- Bottom Metrics Bar -->
                <div class="d-flex align-items-center justify-content-between pt-3 border-top border-secondary border-opacity-25 text-center">
                    <div>
                        <div class="small text-white fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #ffffff !important;">STREAMS</div>
                        <div class="fs-5 fw-bold text-light mt-0.5">2.4M</div>
                    </div>
                    <div class="border-start border-secondary border-opacity-25 ps-4">
                        <div class="small text-white fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #ffffff !important;">ROYALTY</div>
                        <div class="fs-5 fw-bold text-light mt-0.5">₹1.84</div>
                    </div>
                    <div class="border-start border-secondary border-opacity-25 ps-4">
                        <div class="small text-white fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #ffffff !important;">GROWTH</div>
                        <div class="fs-5 fw-bold text-teal mt-0.5">+38%</div>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- =========================================================================
         2. FEATURES SECTION (Exact match to Image 1)
         ========================================================================= -->
    <div class="text-center py-5 my-3">
        
        <!-- Section Tag -->
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill mb-3" style="background: rgba(0, 210, 170, 0.08); border: 1px solid rgba(0, 210, 170, 0.35);">
            <i class="fa-solid fa-wand-magic-sparkles text-teal" style="font-size: 0.8rem;"></i>
            <span class="small fw-semibold text-teal">Why artists choose us</span>
        </div>

        <h2 class="display-5 fw-extrabold text-light mb-2" style="letter-spacing: -0.5px;">
            Everything you need to <span style="background: linear-gradient(135deg, #00d2aa 0%, #22d3ee 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">earn from your music</span>
        </h2>
        <p class="text-white fs-6 mb-5" style="color: #ffffff !important;">
            A modern platform that turns streams into steady income.
        </p>

        <!-- 4 Cards Grid -->
        <div class="row g-4 text-start">
            
            <!-- Card 1: Global Distribution -->
            <div class="col-sm-6 col-lg-3">
                <div class="h-100 p-4 rounded-4" style="background: #0d1526; border: 1px solid rgba(255, 255, 255, 0.08); transition: transform 0.2s ease;">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: rgba(0, 210, 170, 0.12); color: var(--teal); font-size: 1.15rem;">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <h3 class="h5 fw-bold text-light mb-2">Global Distribution</h3>
                    <p class="text-white small mb-0" style="line-height: 1.6; color: #ffffff !important;">
                        Push your tracks to Spotify, Apple Music, JioSaavn, YouTube &amp; 150+ stores in one click.
                    </p>
                </div>
            </div>

            <!-- Card 2: Live Earnings -->
            <div class="col-sm-6 col-lg-3">
                <div class="h-100 p-4 rounded-4" style="background: #0d1526; border: 1px solid rgba(255, 255, 255, 0.08); transition: transform 0.2s ease;">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: rgba(0, 210, 170, 0.12); color: var(--teal); font-size: 1.15rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                    <h3 class="h5 fw-bold text-light mb-2">Live Earnings</h3>
                    <p class="text-white small mb-0" style="line-height: 1.6; color: #ffffff !important;">
                        Watch royalties land in real time. Withdraw to your bank once your profile is verified.
                    </p>
                </div>
            </div>

            <!-- Card 3: Growth Analytics -->
            <div class="col-sm-6 col-lg-3">
                <div class="h-100 p-4 rounded-4" style="background: #0d1526; border: 1px solid rgba(255, 255, 255, 0.08); transition: transform 0.2s ease;">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: rgba(0, 210, 170, 0.12); color: var(--teal); font-size: 1.15rem;">
                        <i class="fa-solid fa-chart-simple"></i>
                    </div>
                    <h3 class="h5 fw-bold text-light mb-2">Growth Analytics</h3>
                    <p class="text-white small mb-0" style="line-height: 1.6; color: #ffffff !important;">
                        Streams, listeners, and revenue &mdash; a fintech-grade dashboard built for artists.
                    </p>
                </div>
            </div>

            <!-- Card 4: Secure & Verified -->
            <div class="col-sm-6 col-lg-3">
                <div class="h-100 p-4 rounded-4" style="background: #0d1526; border: 1px solid rgba(255, 255, 255, 0.08); transition: transform 0.2s ease;">
                    <div class="mb-3 d-inline-flex align-items-center justify-content-center rounded-3" style="width: 44px; height: 44px; background: rgba(0, 210, 170, 0.12); color: var(--teal); font-size: 1.15rem;">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="h5 fw-bold text-light mb-2">Secure &amp; Verified</h3>
                    <p class="text-white small mb-0" style="line-height: 1.6; color: #ffffff !important;">
                        KYC-protected payouts and an admin console that watches every account.
                    </p>
                </div>
            </div>

        </div>

    </div>

    <!-- =========================================================================
         3. BOTTOM BANNER CARD (Exact match to Image 1)
         ========================================================================= -->
    <div class="my-5 p-4 p-md-5 rounded-4 position-relative" style="background: #0d1526; border: 1px solid rgba(255, 255, 255, 0.08); overflow: hidden;">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <h3 class="display-6 fw-bold text-light mb-2">
                    Your growth, live on screen
                </h3>
                <p class="text-white mb-4" style="max-width: 500px; color: #ffffff !important;">
                    Every customer dashboard opens with an animated high-growth chart and a live earnings ticker.
                </p>
                <a href="{{ route('register') }}" class="btn-teal fs-6 py-2.5 px-4">
                    <span>Create your account</span>
                    <i class="fa-solid fa-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="col-lg-5">
                <div style="height: 120px;" class="position-relative">
                    <canvas id="footerChart" class="w-100 h-100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         4. COPYRIGHT CLAIM REMOVE LINK SECTION (Exact match to User Mockup)
         ========================================================================= -->
    <div class="my-5 p-4 p-md-5 rounded-4 position-relative" style="background: #0d1526; border: 1px solid rgba(255, 255, 255, 0.08);">
        
        <!-- Header -->
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="fa-solid fa-link text-teal fs-4"></i>
                <h2 class="h3 fw-bold text-light mb-0 d-inline-flex align-items-center gap-2">
                    <span>Copyright Claim Remove Link</span>
                    <span style="font-size: 1.3rem;">🔗</span>
                </h2>
            </div>
            <p class="small mb-0" style="color: #94a3b8 !important;">
                Verified submissions from our artists, listed in order from 1 to 10.
            </p>
        </div>

        <!-- 10 Slots Grid: 2 columns on desktop (1 to 10) -->
        <div class="row g-3">
            @for($slot = 1; $slot <= 10; $slot++)
                @php $link = isset($claimLinks) ? $claimLinks->get($slot) : null; @endphp
                <div class="col-md-6">
                    @if($link)
                        <!-- Populated Slot Card -->
                        <div class="p-3 px-3.5 rounded-3 d-flex align-items-center gap-3 h-100 position-relative" style="background: #111a2e; border: 1px solid rgba(255, 255, 255, 0.08); transition: border-color 0.2s ease, transform 0.2s ease;">
                            <!-- Serial Badge -->
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; border-radius: 50%; background: rgba(0, 210, 170, 0.12); border: 1px solid rgba(0, 210, 170, 0.35); color: #00d2aa; font-size: 0.9rem;">
                                {{ $slot }}
                            </div>
                            <!-- Link Details -->
                            <div class="flex-grow-1 overflow-hidden" style="min-width: 0;">
                                <div class="fw-bold text-light text-truncate" style="font-size: 0.95rem;">
                                    {{ $link->display_title }}
                                </div>
                                <div class="d-flex align-items-center gap-1.5 mt-0.5 overflow-hidden">
                                    <i class="fa-solid fa-link text-teal flex-shrink-0" style="font-size: 0.72rem;"></i>
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer" class="text-truncate d-inline-block small" style="color: #38bdf8 !important; text-decoration: none; max-width: 100%;" title="{{ $link->url }}">
                                        {{ $link->url }}
                                    </a>
                                </div>
                                <div class="small mt-0.5" style="color: #94a3b8 !important; font-size: 0.78rem;">
                                    by {{ $link->user->username ?: $link->user->name }}
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Available Slot Card -->
                        <div class="p-3 px-3.5 rounded-3 d-flex align-items-center gap-3 h-100 position-relative" style="background: #0d1526; border: 1px solid rgba(255, 255, 255, 0.05); min-height: 72px;">
                            <!-- Dimmed Serial Badge -->
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center fw-semibold" style="width: 34px; height: 34px; border-radius: 50%; background: rgba(255, 255, 255, 0.02); border: 1px solid rgba(0, 210, 170, 0.2); color: #00d2aa; font-size: 0.9rem; opacity: 0.75;">
                                {{ $slot }}
                            </div>
                            <!-- Slot Available Text -->
                            <div class="flex-grow-1">
                                <span style="color: #64748b !important; font-weight: 500; font-size: 0.92rem;">Slot available</span>
                            </div>
                        </div>
                    @endif
                </div>
            @endfor
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    // Smooth Neon Chart for Hero Card & Bottom Banner
    function drawWaveChart(canvasId) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = canvas.offsetWidth;
        let height = canvas.height = canvas.offsetHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = canvas.offsetWidth;
            height = canvas.height = canvas.offsetHeight;
        });

        const points = [
            0.75, 0.65, 0.70, 0.60, 0.62, 0.50, 0.55, 0.45, 
            0.40, 0.50, 0.42, 0.35, 0.28, 0.32, 0.22, 0.15
        ];

        let offset = 0;

        function animate() {
            ctx.clearRect(0, 0, width, height);

            ctx.beginPath();
            ctx.strokeStyle = '#00d2aa';
            ctx.lineWidth = 3;
            ctx.shadowColor = 'rgba(0, 210, 170, 0.8)';
            ctx.shadowBlur = 14;

            const step = width / (points.length - 1);

            for (let i = 0; i < points.length; i++) {
                const x = i * step;
                const wave = Math.sin(offset + i * 0.4) * 6;
                const y = (points[i] * height) + wave;

                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    const prevX = (i - 1) * step;
                    const prevY = (points[i - 1] * height) + Math.sin(offset + (i - 1) * 0.4) * 6;
                    const cpX = (prevX + x) / 2;
                    ctx.quadraticCurveTo(cpX, prevY, x, y);
                }
            }
            ctx.stroke();

            // Glow dot at the very end
            const lastIdx = points.length - 1;
            const lastX = lastIdx * step;
            const lastY = (points[lastIdx] * height) + Math.sin(offset + lastIdx * 0.4) * 6;

            ctx.beginPath();
            ctx.fillStyle = '#ffffff';
            ctx.shadowColor = '#00d2aa';
            ctx.shadowBlur = 20;
            ctx.arc(lastX, lastY, 4, 0, Math.PI * 2);
            ctx.fill();

            offset += 0.03;
            requestAnimationFrame(animate);
        }

        animate();
    }

    document.addEventListener('DOMContentLoaded', () => {
        drawWaveChart('heroChart');
        drawWaveChart('footerChart');
    });
</script>
@endsection
