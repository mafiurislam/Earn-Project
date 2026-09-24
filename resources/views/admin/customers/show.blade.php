@extends('layouts.app')

@section('title', 'Customer Profile: ' . $customer->name . ' - Admin Console')

@section('content')
<div class="container-fluid px-3 px-sm-4 px-lg-5 py-4">

    <!-- =========================================================================
         BREADCRUMB & HEADER ACTION
         ========================================================================= -->
    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-dark-outline rounded-pill py-1.5 px-3 fw-semibold d-inline-flex align-items-center gap-1.5" style="border-color: rgba(255, 255, 255, 0.12);">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Admin Dashboard</span>
            </a>
            <span class="text-secondary opacity-50">/</span>
            <span class="text-teal small fw-bold text-uppercase" style="letter-spacing: 1px;">Customer Profile</span>
        </div>
        <div>
            <span class="badge px-3 py-2 rounded-pill small d-inline-flex align-items-center gap-2" style="background: rgba(0, 210, 170, 0.12); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.35); box-shadow: 0 0 16px rgba(0, 210, 170, 0.15);">
                <span class="d-inline-block rounded-circle bg-teal" style="width: 7px; height: 7px; box-shadow: 0 0 8px #00d2aa;"></span>
                <i class="fa-solid fa-shield-halved"></i>
                <span>Admin Full CRUD Access</span>
            </span>
        </div>
    </div>

    <!-- =========================================================================
         HEADER PROFILE HERO CARD (Customer Details & Action Toolbar)
         ========================================================================= -->
    <div class="profile-hero-card mb-4">
        <div class="profile-hero-content">
            <div class="row align-items-center g-4">
                
                <!-- Left: Avatar & Basic Info -->
                <div class="col-lg-7">
                    <div class="d-flex flex-column flex-sm-row align-items-center align-items-sm-start text-center text-sm-start gap-3.5">
                        
                        <!-- Avatar with multi-color glowing ring -->
                        <div class="profile-avatar-ring">
                            <div class="profile-avatar-inner">
                                @if($customer->profile_photo)
                                    <img src="{{ $customer->profile_photo_url }}" alt="{{ $customer->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    @php
                                        $uNames = explode(' ', $customer->name);
                                        $uInitials = strtoupper(substr($uNames[0] ?? 'C', 0, 1) . (isset($uNames[1]) ? substr($uNames[1], 0, 1) : ''));
                                    @endphp
                                    <span>{{ $uInitials ?: 'CU' }}</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-center justify-content-sm-start gap-2 flex-wrap mb-2">
                                <h1 class="h3 fw-extrabold text-light mb-0" style="letter-spacing: -0.5px;">{{ $customer->name }}</h1>
                                
                                <!-- KYC status badge -->
                                @if($customer->isVerified())
                                    <span class="badge px-2.5 py-1 rounded-pill small fw-bold" style="background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.35);">
                                        <i class="fa-solid fa-circle-check me-1"></i> Verified KYC
                                    </span>
                                @elseif($customer->verification && $customer->verification->status === 'pending')
                                    <span class="badge px-2.5 py-1 rounded-pill small fw-bold" style="background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.35);">
                                        <i class="fa-solid fa-clock me-1"></i> KYC Pending
                                    </span>
                                @elseif($customer->verification && $customer->verification->status === 'rejected')
                                    <span class="badge px-2.5 py-1 rounded-pill small fw-bold" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.35);">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> KYC Rejected
                                    </span>
                                @else
                                    <span class="badge px-2.5 py-1 rounded-pill small fw-bold" style="background: rgba(148, 163, 184, 0.12); color: #94a3b8; border: 1px solid rgba(148, 163, 184, 0.25);">
                                        <i class="fa-solid fa-shield-slash me-1"></i> Unverified
                                    </span>
                                @endif

                                <!-- Autocart Generator status badge -->
                                @if($customer->autocart_generator_enabled)
                                    <span class="badge px-3 py-1 rounded-pill small fw-bold" style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.4); color: #10b981 !important; box-shadow: 0 0 12px rgba(16, 185, 129, 0.2);">
                                        <i class="fa-solid fa-circle-check me-1"></i> Autocart Generator is ON
                                    </span>
                                @else
                                    <span class="badge px-3 py-1 rounded-pill small fw-bold" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.4); color: #ef4444 !important; box-shadow: 0 0 12px rgba(239, 68, 68, 0.2);">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Autocart Generator is OFF
                                    </span>
                                @endif
                            </div>

                            <!-- Interactive Contact Chips -->
                            <div class="contact-chip-group mb-2.5">
                                <a href="mailto:{{ $customer->email }}" class="contact-chip contact-chip-email" title="Email Customer">
                                    <i class="fa-regular fa-envelope text-teal"></i>
                                    <span>{{ $customer->email }}</span>
                                </a>
                                @if($customer->phone)
                                    <a href="tel:{{ $customer->phone }}" class="contact-chip contact-chip-phone" title="Call Customer">
                                        <i class="fa-solid fa-phone text-info"></i>
                                        <span>{{ $customer->phone }}</span>
                                    </a>
                                @endif
                                @if($customer->username)
                                    <span class="contact-chip contact-chip-user" title="Username">
                                        <i class="fa-regular fa-user" style="color: #a855f7;"></i>
                                        <span>@ {{ $customer->username }}</span>
                                    </span>
                                @endif
                            </div>

                            <div class="small d-flex align-items-center justify-content-center justify-content-sm-start gap-3 flex-wrap" style="color: #94a3b8; font-size: 0.8rem;">
                                <span><i class="fa-solid fa-hashtag text-secondary me-1"></i> Customer ID: <strong class="text-light">#CUST-{{ $customer->id }}</strong></span>
                                <span>&bull;</span>
                                <span><i class="fa-regular fa-calendar text-secondary me-1"></i> Registered on: <strong class="text-light">{{ $customer->created_at->format('M d, Y') }}</strong></span>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right: Admin Action Toolbar -->
                <div class="col-lg-5">
                    <div class="action-toolbar-grid">
                        
                        <!-- Upload Song for this Customer -->
                        <button type="button" class="btn-hero-primary" data-bs-toggle="modal" data-bs-target="#adminAddSongModal">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Upload Song</span>
                        </button>

                        <!-- Edit Customer Info -->
                        <button type="button" class="btn-hero-cyan" data-bs-toggle="modal" data-bs-target="#editCustomerModal">
                            <i class="fa-regular fa-pen-to-square"></i>
                            <span>Edit Customer</span>
                        </button>

                        <!-- Adjust Earnings -->
                        <button type="button" class="btn-hero-amber" data-bs-toggle="modal" data-bs-target="#manageEarningsModal">
                            <i class="fa-solid fa-coins"></i>
                            <span>Adjust Balance</span>
                        </button>

                        <!-- Delete Customer Profile -->
                        <form action="{{ route('admin.customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('WARNING: Are you sure you want to permanently delete this customer profile for {{ $customer->name }}?\n\nThis will permanently delete all of this customer\'s uploaded songs, MP3 audio files, 3000x3000px cover images, KYC records, and earnings data.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-hero-crimson" title="Permanently Delete Customer Profile">
                                <i class="fa-regular fa-trash-can"></i>
                                <span>Delete Profile</span>
                            </button>
                        </form>

                    </div>
                </div>

            </div>

            <!-- 4 Key Stats Row for this Customer (2x2 on mobile, 4 in a row on desktop) -->
            <div class="row g-3 mt-4 pt-3 border-top border-secondary border-opacity-25">
                
                <!-- 1. Uploaded Songs -->
                <div class="col-6 col-lg-3">
                    <div class="stat-card-metric metric-songs">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-uppercase small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #cbd5e1;">UPLOADED SONGS</div>
                            <div class="stat-icon-badge" style="background: rgba(129, 140, 248, 0.12); color: #818cf8;">
                                <i class="fa-solid fa-compact-disc"></i>
                            </div>
                        </div>
                        <div class="fs-4 fw-extrabold text-light mb-1">
                            {{ $customer->songs->count() }} <span class="fs-6 fw-semibold text-secondary">Tracks</span>
                        </div>
                        <div class="small d-flex align-items-center gap-1.5" style="font-size: 0.75rem; color: #818cf8;">
                            <i class="fa-solid fa-music"></i> <span>Catalog Audio</span>
                        </div>
                    </div>
                </div>

                <!-- 2. Available Balance -->
                <div class="col-6 col-lg-3">
                    <div class="stat-card-metric metric-balance">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-uppercase small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #cbd5e1;">AVAILABLE BALANCE</div>
                            <div class="stat-icon-badge" style="background: rgba(0, 210, 170, 0.12); color: #00d2aa;">
                                <i class="fa-solid fa-wallet"></i>
                            </div>
                        </div>
                        <div class="fs-4 fw-extrabold text-teal mb-1">
                            ₹{{ number_format($customer->earning_balance, 2) }}
                        </div>
                        <div class="small d-flex align-items-center gap-1.5" style="font-size: 0.75rem; color: #00d2aa;">
                            <i class="fa-solid fa-arrow-down-to-bracket"></i> <span>Ready for Payout</span>
                        </div>
                    </div>
                </div>

                <!-- 3. Total Lifetime Royalties -->
                <div class="col-6 col-lg-3">
                    <div class="stat-card-metric metric-earnings">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-uppercase small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #cbd5e1;">TOTAL EARNINGS</div>
                            <div class="stat-icon-badge" style="background: rgba(245, 158, 11, 0.12); color: #f59e0b;">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                        </div>
                        <div class="fs-4 fw-extrabold mb-1" style="color: #fbbf24;">
                            ₹{{ number_format($customer->total_earnings, 2) }}
                        </div>
                        <div class="small d-flex align-items-center gap-1.5" style="font-size: 0.75rem; color: #f59e0b;">
                            <i class="fa-solid fa-chart-line"></i> <span>Gross Revenue</span>
                        </div>
                    </div>
                </div>

                <!-- 4. Total Withdrawn -->
                <div class="col-6 col-lg-3">
                    <div class="stat-card-metric metric-paidout">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="text-uppercase small fw-bold" style="font-size: 0.72rem; letter-spacing: 0.5px; color: #cbd5e1;">TOTAL PAID OUT</div>
                            <div class="stat-icon-badge" style="background: rgba(56, 189, 248, 0.12); color: #38bdf8;">
                                <i class="fa-solid fa-arrow-trend-up"></i>
                            </div>
                        </div>
                        <div class="fs-4 fw-extrabold mb-1" style="color: #38bdf8;">
                            ₹{{ number_format($customer->withdrawn_amount, 2) }}
                        </div>
                        <div class="small d-flex align-items-center gap-1.5" style="font-size: 0.75rem; color: #38bdf8;">
                            <i class="fa-solid fa-circle-check"></i> <span>Disbursed via Bank</span>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!-- =========================================================================
         TABBED CONTENT SECTIONS: SONGS / COPYRIGHT LINKS / KYC / FINANCIALS
         ========================================================================= -->
    <ul class="nav nav-pills-modern mb-4" id="customerProfileTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="songs-tab" data-bs-toggle="pill" data-bs-target="#songs-content" type="button">
                <i class="fa-solid fa-music"></i>
                <span>Uploaded Songs ({{ $customer->songs->count() }})</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="copyright-tab" data-bs-toggle="pill" data-bs-target="#copyright-content" type="button">
                <i class="fa-solid fa-link"></i>
                <span>Copyright Claim Links ({{ $customer->copyrightClaimLinks->count() }})</span>
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="kyc-tab" data-bs-toggle="pill" data-bs-target="#kyc-content" type="button">
                <i class="fa-solid fa-shield-halved"></i>
                <span>KYC Verification</span>
                @if($customer->verification && $customer->verification->status === 'pending')
                    <span class="badge rounded-pill bg-warning text-dark ms-1">1</span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="profile-info-tab" data-bs-toggle="pill" data-bs-target="#profile-info-content" type="button">
                <i class="fa-solid fa-id-card"></i>
                <span>Profile Info</span>
                @if($customer->isProfileComplete())
                    <span class="profile-status-dot dot-green ms-1" title="Profile Complete (Green)"></span>
                @else
                    <span class="profile-status-dot dot-red ms-1" title="Profile Incomplete (Red)"></span>
                @endif
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="financials-tab" data-bs-toggle="pill" data-bs-target="#financials-content" type="button">
                <i class="fa-solid fa-receipt"></i>
                <span>Royalties &amp; Withdrawals</span>
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <!-- =====================================================================
             TAB 1: UPLOADED SONGS (COMPLETE ADMIN CRUD)
             ===================================================================== -->
        <div class="tab-pane fade show active" id="songs-content" role="tabpanel">
            <div class="glass-tab-panel mb-4">
                
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div style="width: 32px; height: 32px; border-radius: 8px; background: rgba(0, 210, 170, 0.14); display: flex; align-items: center; justify-content: center; color: #00d2aa;">
                                <i class="fa-solid fa-compact-disc fs-6"></i>
                            </div>
                            <h2 class="h5 fw-bold text-light mb-0">Customer's Uploaded Songs &amp; Files</h2>
                            <span class="badge rounded-pill fw-bold text-dark px-2.5 py-1 small" style="background: #00d2aa;">
                                {{ $customer->songs->count() }} Available
                            </span>
                        </div>
                        <p class="text-secondary small mb-0 mt-1">
                            All songs uploaded by {{ $customer->name }} are stored and displayed here. Full CRUD management is available: listen to MP3 files, inspect 3000 &times; 3000 px artwork, edit song details, or delete files.
                        </p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-sm btn-hero-primary py-2 px-3.5 text-nowrap" data-bs-toggle="modal" data-bs-target="#adminAddSongModal">
                            <i class="fa-solid fa-plus me-1"></i> Add Song for {{ $customer->name }}
                        </button>
                    </div>
                </div>

                @if($customer->songs->count() === 0)
                    <div class="text-center py-5 rounded-4" style="background: rgba(255, 255, 255, 0.02); border: 1px dashed rgba(255, 255, 255, 0.1);">
                        <i class="fa-solid fa-music text-teal display-5 mb-2 d-block opacity-50"></i>
                        <h6 class="fw-bold text-light mb-1">No songs uploaded yet for this customer.</h6>
                        <p class="text-secondary small mb-3">When the customer uploads a song from their profile, it will appear here instantly, or you can upload one now.</p>
                        <button type="button" class="btn btn-sm btn-hero-primary py-2 px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#adminAddSongModal">
                            <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload First Song
                        </button>
                    </div>
                @else
                    <div class="d-flex flex-column gap-3">
                        @foreach($customer->songs as $song)
                            <div class="song-catalog-card">
                                <div class="row align-items-center g-3">
                                    
                                    <!-- 3000x3000px Cover Thumbnail -->
                                    <div class="col-auto">
                                        <div class="song-cover-thumb">
                                            <img src="{{ $song->cover_image_url }}" alt="{{ $song->title }}">
                                            <!-- Zoom Preview Button -->
                                            <button type="button" class="btn btn-sm position-absolute top-0 end-0 p-1 m-1 rounded-circle" style="background: rgba(0,0,0,0.7); color: #00d2aa; border: none; line-height: 1;" title="View 3000 × 3000 px Cover Art" data-bs-toggle="modal" data-bs-target="#adminCoverZoomModal{{ $song->id }}">
                                                <i class="fa-solid fa-magnifying-glass-plus" style="font-size: 0.72rem;"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Song Info Grid -->
                                    <div class="col-md-5 col-lg-4">
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            <h4 class="h6 fw-extrabold text-light mb-0 text-truncate" style="max-width: 200px;">{{ $song->title }}</h4>
                                            <span class="badge rounded-pill small px-2 py-0.5" style="background: rgba(0, 210, 170, 0.15); color: #00d2aa; font-size: 0.68rem; border: 1px solid rgba(0, 210, 170, 0.3);">
                                                HQ 3000&times;3000
                                            </span>
                                        </div>
                                        <div class="small text-truncate mb-1">
                                            <span class="badge px-2 py-0.5 rounded-pill small fw-semibold" style="background: rgba(0, 210, 170, 0.1); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.25);">
                                                <i class="fa-solid fa-microphone me-1"></i> Singer: {{ $song->singer }}
                                            </span>
                                        </div>
                                        <div class="small text-secondary text-truncate" style="font-size: 0.78rem;">
                                            <span>Lyrics/Composer: <strong class="text-light">{{ $song->composer }}</strong></span><br>
                                            <span>Producer: <strong class="text-light">{{ $song->producer }}</strong></span>
                                        </div>
                                        <div class="mt-2 d-inline-flex align-items-center gap-1.5 px-2.5 py-0.5 rounded-pill small fw-semibold" style="background: rgba(0, 210, 170, 0.08); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.25); font-size: 0.74rem;">
                                            <i class="fa-regular fa-copyright"></i> {{ $song->copyright ?: '℗ 2026 Rajdoot Nivedan' }}
                                        </div>
                                    </div>

                                    <!-- MP3 Audio Player -->
                                    <div class="col-md-4 col-lg-4">
                                        <div class="d-flex flex-column gap-1 p-2.5 rounded-3" style="background: rgba(0, 0, 0, 0.25); border: 1px solid rgba(255, 255, 255, 0.06);">
                                            <div class="d-flex align-items-center justify-content-between small" style="font-size: 0.75rem;">
                                                <span class="text-teal fw-semibold"><i class="fa-solid fa-play me-1"></i> MP3 File Stream</span>
                                                <span class="text-secondary">{{ $song->created_at->format('M d, Y') }}</span>
                                            </div>
                                            <audio controls class="w-100" style="height: 36px; border-radius: 6px;" preload="metadata" src="{{ $song->audio_file_url }}"></audio>
                                            <div class="d-flex justify-content-between small text-white" style="font-size: 0.72rem;">
                                                <a href="{{ $song->audio_file_url }}" download target="_blank" class="text-decoration-none text-info fw-semibold">
                                                    <i class="fa-solid fa-download me-1"></i> Download MP3
                                                </a>
                                                <span class="badge px-2 py-0.5 rounded-pill" style="background: rgba(16, 185, 129, 0.15); color: #10b981;">Active</span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Admin CRUD Actions: Edit & Delete -->
                                    <div class="col-12 col-md-auto col-lg-auto ms-md-auto text-end">
                                        <div class="d-flex d-md-inline-flex gap-2 w-100 justify-content-end">
                                            <!-- Edit Song -->
                                            <button type="button" class="btn btn-sm btn-hero-cyan py-1.5 px-3 w-50 w-md-auto" data-bs-toggle="modal" data-bs-target="#adminEditSongModal{{ $song->id }}" title="Edit Song Details">
                                                <i class="fa-regular fa-pen-to-square me-1"></i> Edit
                                            </button>

                                            <!-- Delete Song -->
                                            <form action="{{ route('admin.songs.destroy', $song->id) }}" method="POST" class="d-inline w-50 w-md-auto" onsubmit="return confirm('Are you sure you want to permanently delete the song &quot;{{ $song->title }}&quot; and its MP3/artwork files?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-hero-crimson py-1.5 px-3 w-100" title="Delete Song">
                                                    <i class="fa-regular fa-trash-can me-1"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- MODAL: ADMIN ZOOM 3000x3000px COVER ART -->
                            <div class="modal fade" id="adminCoverZoomModal{{ $song->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(0,210,170,0.3); border-radius: 20px;">
                                        <div class="modal-header border-secondary border-opacity-25">
                                            <h5 class="modal-title fw-bold text-light">
                                                <i class="fa-regular fa-image text-teal me-2"></i> 3000 &times; 3000 px Cover: {{ $song->title }}
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-4 text-center">
                                            <div class="p-2 rounded-3 bg-black border border-secondary border-opacity-25 d-inline-block">
                                                <img src="{{ $song->cover_image_url }}" alt="{{ $song->title }}" class="img-fluid rounded" style="max-height: 480px; object-fit: contain;">
                                            </div>
                                            <div class="mt-3 d-flex align-items-center justify-content-center gap-3">
                                                <span class="badge px-3 py-1.5 rounded-pill" style="background: rgba(0, 210, 170, 0.15); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.35);">
                                                    Full 3000 &times; 3000 px High-Res Artwork
                                                </span>
                                                <a href="{{ $song->cover_image_url }}" download="{{ Str::slug($song->title) }}-cover.jpg" target="_blank" class="btn btn-sm btn-hero-primary py-1.5 px-3 rounded-pill fw-bold">
                                                    <i class="fa-solid fa-download me-1"></i> Download Cover
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- MODAL: ADMIN EDIT SONG -->
                            <div class="modal fade" id="adminEditSongModal{{ $song->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;">
                                        <div class="modal-header border-secondary border-opacity-25">
                                            <h5 class="modal-title fw-bold text-light">
                                                <i class="fa-solid fa-pen-to-square text-teal me-2"></i> Edit Song: {{ $song->title }} (Customer: {{ $customer->name }})
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('admin.songs.update', $song->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4 text-start">
                                                <div class="row g-3">
                                                    
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-white">Song Title</label>
                                                        <input type="text" name="title" class="auth-input" style="padding-left: 14px;" value="{{ $song->title }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-white">Singer</label>
                                                        <input type="text" name="singer" class="auth-input" style="padding-left: 14px;" value="{{ $song->singer }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-white">Lyrics / Composer</label>
                                                        <input type="text" name="composer" class="auth-input" style="padding-left: 14px;" value="{{ $song->composer }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-white">Producer</label>
                                                        <input type="text" name="producer" class="auth-input" style="padding-left: 14px;" value="{{ $song->producer }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-white">Replace 3000 &times; 3000 px Cover Art (Optional)</label>
                                                        <input type="file" name="cover_image" class="auth-input" style="padding-left: 14px;" accept="image/jpeg,image/png,image/jpg,image/webp">
                                                        <small class="text-secondary d-block mt-1">Leave blank to retain existing artwork.</small>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold text-white">Replace MP3 Audio Track (Optional)</label>
                                                        <input type="file" name="audio_file" class="auth-input" style="padding-left: 14px;" accept=".mp3,audio/mpeg,audio/mp3">
                                                        <small class="text-secondary d-block mt-1">Leave blank to retain existing audio file.</small>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label small fw-semibold text-white">Copyright &mdash; P-Line <span class="text-teal">(Auto-Included)</span></label>
                                                        <input type="text" name="copyright" class="auth-input" style="padding-left: 14px; background: rgba(0, 210, 170, 0.05); color: #00d2aa; font-weight: 600;" value="{{ $song->copyright ?: '℗ 2026 Rajdoot Nivedan' }}" readonly>
                                                    </div>

                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary border-opacity-25">
                                                <button type="button" class="btn btn-dark-outline py-2 px-3.5" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-hero-primary py-2 px-4 rounded-pill fw-bold">Update Song</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>

        <!-- =====================================================================
             TAB 2: COPYRIGHT CLAIM REMOVE LINKS
             ===================================================================== -->
        <div class="tab-pane fade" id="copyright-content" role="tabpanel">
            <div class="glass-tab-panel mb-4">
                
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-link text-teal fs-5"></i>
                        <h3 class="h5 fw-bold text-light mb-0">Copyright Claim Remove Links by {{ $customer->name }}</h3>
                    </div>
                    <span class="badge rounded-pill small px-3 py-1.5" style="background: rgba(0, 210, 170, 0.15); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.35);">
                        {{ $customer->copyrightClaimLinks->count() }} Uploaded Links
                    </span>
                </div>

                @if($customer->copyrightClaimLinks->count() === 0)
                    <div class="text-center py-4 rounded-4 text-white small" style="background: rgba(255, 255, 255, 0.02); border: 1px dashed rgba(255, 255, 255, 0.1);">
                        <i class="fa-solid fa-link-slash text-teal fs-4 d-block mb-2 opacity-50"></i>
                        No copyright claim removal links uploaded by this customer yet.
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table-custom-dark">
                            <thead>
                                <tr>
                                    <th style="width: 90px;">Slot #</th>
                                    <th>Title</th>
                                    <th>URL</th>
                                    <th>Last Updated</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($customer->copyrightClaimLinks as $cl)
                                    <tr>
                                        <td>
                                            <div class="d-inline-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; border-radius: 50%; background: rgba(0, 210, 170, 0.15); border: 1px solid rgba(0, 210, 170, 0.4); color: #00d2aa; font-size: 0.88rem;">
                                                {{ $cl->slot_number }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-light">{{ $cl->display_title }}</span>
                                        </td>
                                        <td>
                                            <a href="{{ $cl->url }}" target="_blank" rel="noopener noreferrer" class="small text-decoration-none text-truncate d-inline-block" style="color: #38bdf8 !important; max-width: 320px;">
                                                {{ $cl->url }}
                                            </a>
                                        </td>
                                        <td class="small text-white">
                                            {{ $cl->updated_at->format('M d, Y') }}
                                        </td>
                                        <td class="text-end">
                                            <form action="{{ route('admin.copyright_links.destroy', $cl->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this link from Slot #{{ $cl->slot_number }}?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-dark-outline py-1 px-2.5 rounded-pill text-danger border-danger border-opacity-40">
                                                    <i class="fa-solid fa-trash me-1"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

            </div>
        </div>

        <!-- =====================================================================
             TAB 3: KYC & VERIFICATION
             ===================================================================== -->
        <div class="tab-pane fade" id="kyc-content" role="tabpanel">
            <div class="glass-tab-panel mb-4">
                
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-shield-halved text-teal fs-5"></i>
                        <h3 class="h5 fw-bold text-light mb-0">Identity &amp; Bank KYC Verification</h3>
                    </div>
                    <div>
                        @if($customer->isVerified())
                            <span class="badge-approved px-3 py-2"><i class="fa-solid fa-circle-check me-1"></i> Verified Account</span>
                        @elseif($customer->verification && $customer->verification->status === 'pending')
                            <span class="badge-pending px-3 py-2"><i class="fa-solid fa-clock me-1"></i> Awaiting Admin Decision</span>
                        @elseif($customer->verification && $customer->verification->status === 'rejected')
                            <span class="badge-rejected px-3 py-2"><i class="fa-solid fa-xmark me-1"></i> Application Rejected</span>
                        @else
                            <span class="badge-unverified px-3 py-2"><i class="fa-solid fa-lock me-1"></i> Not Submitted Yet</span>
                        @endif
                    </div>
                </div>

                @if($customer->verification)
                    <div class="row g-4">
                        
                        <!-- KYC Details Table -->
                        <div class="col-lg-6">
                            <div class="p-4 rounded-4 h-100" style="background: #111a2e; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <h6 class="fw-bold text-teal text-uppercase small mb-3">Submitted Banking &amp; Identity Data</h6>
                                
                                <div class="d-flex flex-column gap-3 small">
                                    <div class="d-flex justify-content-between pb-2 border-bottom border-secondary border-opacity-25">
                                        <span class="text-secondary">Full Legal Name (PAN):</span>
                                        <strong class="text-light">{{ $customer->verification->full_name }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between pb-2 border-bottom border-secondary border-opacity-25">
                                        <span class="text-secondary">PAN Card Number:</span>
                                        <strong class="text-teal font-monospace">{{ $customer->verification->pan_number }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between pb-2 border-bottom border-secondary border-opacity-25">
                                        <span class="text-secondary">Bank Account Number:</span>
                                        <strong class="text-light font-monospace">{{ $customer->verification->bank_account }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between pb-2 border-bottom border-secondary border-opacity-25">
                                        <span class="text-secondary">IFSC Code:</span>
                                        <strong class="text-info font-monospace">{{ $customer->verification->ifsc_code }}</strong>
                                    </div>
                                    <div class="d-flex justify-content-between pb-2 border-bottom border-secondary border-opacity-25">
                                        <span class="text-secondary">Contact Phone:</span>
                                        <span class="text-light">{{ $customer->verification->phone }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="text-secondary">Contact Email:</span>
                                        <span class="text-light">{{ $customer->verification->email }}</span>
                                    </div>
                                </div>

                                <!-- Action Buttons if pending -->
                                @if($customer->verification->status === 'pending')
                                    <div class="d-flex gap-2 mt-4 pt-3 border-top border-secondary border-opacity-25">
                                        <form action="{{ route('admin.verification.approve', $customer->verification->id) }}" method="POST" class="flex-grow-1">
                                            @csrf
                                            <button type="submit" class="btn btn-hero-primary w-100 py-2 rounded-pill fw-bold">
                                                <i class="fa-solid fa-check me-1"></i> Approve KYC
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-hero-crimson py-2 px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#rejectVerModal{{ $customer->verification->id }}">
                                            <i class="fa-solid fa-xmark me-1"></i> Reject
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Document Photos -->
                        <div class="col-lg-6">
                            <div class="p-4 rounded-4 h-100" style="background: #111a2e; border: 1px solid rgba(255, 255, 255, 0.08);">
                                <h6 class="fw-bold text-info text-uppercase small mb-3">Uploaded Verification Documents</h6>
                                
                                <div class="row g-3">
                                    <div class="col-sm-6 text-center">
                                        <div class="small fw-semibold text-secondary mb-2">PAN Card Image</div>
                                        <div class="p-2 border border-secondary border-opacity-25 rounded-3 bg-black">
                                            @if($customer->verification->pan_card_photo)
                                                <img src="{{ asset('storage/' . $customer->verification->pan_card_photo) }}" alt="PAN Card" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                                            @else
                                                <span class="text-secondary small">No document uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-sm-6 text-center">
                                        <div class="small fw-semibold text-secondary mb-2">Signature Photo</div>
                                        <div class="p-2 border border-secondary border-opacity-25 rounded-3 bg-black">
                                            @if($customer->verification->signature_photo)
                                                <img src="{{ asset('storage/' . $customer->verification->signature_photo) }}" alt="Signature" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                                            @else
                                                <span class="text-secondary small">No document uploaded</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- KYC Rejection Modal if pending -->
                    @if($customer->verification->status === 'pending')
                        <div class="modal fade" id="rejectVerModal{{ $customer->verification->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(239,68,68,0.3); border-radius: 20px;">
                                    <div class="modal-header border-secondary border-opacity-25">
                                        <h5 class="modal-title fw-bold text-light">Reject KYC Verification</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.verification.reject', $customer->verification->id) }}" method="POST">
                                        @csrf
                                        <div class="modal-body p-4 text-start">
                                            <p class="text-secondary small mb-3">Please specify the reason for rejecting {{ $customer->name }}'s KYC documents:</p>
                                            <textarea name="rejection_reason" class="auth-input w-100" rows="3" style="padding: 12px;" placeholder="e.g. Unclear PAN card image or mismatched bank details" required></textarea>
                                        </div>
                                        <div class="modal-footer border-secondary border-opacity-25">
                                            <button type="button" class="btn btn-dark-outline py-1.5 px-3" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger py-1.5 px-3.5 rounded-pill fw-bold">Confirm Rejection</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-4 rounded-4 text-white small" style="background: rgba(255, 255, 255, 0.02); border: 1px dashed rgba(255, 255, 255, 0.1);">
                        <i class="fa-solid fa-shield-slash text-warning fs-3 mb-2 d-block opacity-50"></i>
                        This customer has not submitted their KYC verification documents yet.
                    </div>
                @endif

            </div>
        </div>

        <!-- =====================================================================
             TAB 4: CUSTOMER PROFILE INFO (4 REQUIRED FIELDS)
             ===================================================================== -->
        <div class="tab-pane fade" id="profile-info-content" role="tabpanel">
            <div class="glass-tab-panel mb-4">
                
                <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-25">
                    <div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-id-card text-teal fs-5"></i>
                            <h2 class="h5 fw-bold text-light mb-0">Customer Profile Information</h2>
                            <span class="badge-profile-status {{ $customer->isProfileComplete() ? 'complete' : 'incomplete' }}">
                                <i class="fa-solid {{ $customer->isProfileComplete() ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                                {{ $customer->isProfileComplete() ? 'All 4 Details Completed' : 'Incomplete' }}
                            </span>
                        </div>
                        <p class="text-secondary small mb-0 mt-1">
                            Information submitted by {{ $customer->name }} via their Profile Info portal.
                        </p>
                    </div>
                </div>

                <!-- Profile Info 4 Field Cards -->
                <div class="row g-3">
                    <!-- 1. Owner Name -->
                    <div class="col-md-6">
                        <div class="p-3.5 rounded-3 h-100" style="background: #111a2e; border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-user text-teal me-1.5"></i> Owner Name
                            </div>
                            <div class="text-light fw-bold fs-5">
                                {{ $customer->profileInfo->owner_name ?? '— Not submitted yet —' }}
                            </div>
                        </div>
                    </div>

                    <!-- 2. YouTube Channel Name -->
                    <div class="col-md-6">
                        <div class="p-3.5 rounded-3 h-100" style="background: #111a2e; border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                                <i class="fa-brands fa-youtube text-danger me-1.5"></i> YouTube Channel Name
                            </div>
                            <div class="text-light fw-bold fs-5">
                                {{ $customer->profileInfo->channel_name ?? '— Not submitted yet —' }}
                            </div>
                        </div>
                    </div>

                    <!-- 3. YouTube Link -->
                    <div class="col-md-6">
                        <div class="p-3.5 rounded-3 h-100" style="background: #111a2e; border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-link text-info me-1.5"></i> YouTube Link
                            </div>
                            <div class="fs-6 mt-1">
                                @if(!empty($customer->profileInfo->youtube_link))
                                    <a href="{{ $customer->profileInfo->youtube_link }}" target="_blank" rel="noopener noreferrer" class="text-teal text-decoration-none fw-bold d-inline-flex align-items-center gap-1.5" style="word-break: break-all;">
                                        <span>{{ $customer->profileInfo->youtube_link }}</span>
                                        <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.8rem;"></i>
                                    </a>
                                @else
                                    <span class="text-secondary small">— Not submitted yet —</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 4. Label Name -->
                    <div class="col-md-6">
                        <div class="p-3.5 rounded-3 h-100" style="background: #111a2e; border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-tag text-warning me-1.5"></i> Label Name
                            </div>
                            <div class="text-light fw-bold fs-5">
                                {{ $customer->profileInfo->label_name ?? '— Not submitted yet —' }}
                            </div>
                        </div>
                    </div>

                    <!-- 5. Autocart Generator Status -->
                    <div class="col-md-12">
                        <div class="p-3.5 rounded-3 h-100" style="background: #111a2e; border: 1px solid rgba(255, 255, 255, 0.08);">
                            <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.74rem; letter-spacing: 0.5px;">
                                <i class="fa-solid fa-wand-magic-sparkles text-teal me-1.5"></i> Autocart Generator Status
                            </div>
                            <div class="fs-5 fw-bold">
                                @if($customer->autocart_generator_enabled)
                                    <span class="text-success" style="color: #10b981 !important;">
                                        <i class="fa-solid fa-circle-check me-1"></i> Autocart Generator is ON
                                    </span>
                                @else
                                    <span class="text-danger" style="color: #ef4444 !important;">
                                        <i class="fa-solid fa-circle-xmark me-1"></i> Autocart Generator is OFF
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($customer->profileInfo)
                    <div class="mt-3 pt-3 border-top border-secondary border-opacity-25 d-flex justify-content-between text-secondary small" style="font-size: 0.78rem;">
                        <span>Record ID: #PID-{{ $customer->profileInfo->id }}</span>
                        <span>Last updated: {{ $customer->profileInfo->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                @endif

            </div>
        </div>

        <!-- =====================================================================
             TAB 5: ROYALTIES & FINANCIAL STATEMENT AUDIT
             ===================================================================== -->
        <div class="tab-pane fade" id="financials-content" role="tabpanel">
            <div class="glass-tab-panel mb-4">
                
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-secondary border-opacity-25">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fa-solid fa-receipt text-teal fs-5"></i>
                        <h3 class="h5 fw-bold text-light mb-0">Earnings Statement &amp; Transaction Audit</h3>
                    </div>
                    <button type="button" class="btn btn-sm btn-hero-amber py-1.5 px-3 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#manageEarningsModal">
                        <i class="fa-solid fa-coins me-1"></i> Adjust Balance
                    </button>
                </div>

                <div class="table-responsive" style="max-height: 380px;">
                    <table class="table-custom-dark">
                        <thead>
                            <tr>
                                <th>Date &amp; Time</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance After</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->earningTransactions as $tx)
                                <tr>
                                    <td class="small text-secondary">{{ $tx->created_at->format('M d, Y, h:i A') }}</td>
                                    <td>
                                        @if($tx->type === 'credit')
                                            <span class="badge-approved">CREDIT</span>
                                        @elseif($tx->type === 'debit')
                                            <span class="badge-rejected">DEBIT</span>
                                        @else
                                            <span class="badge-pending">ADJUSTMENT</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ $tx->type === 'credit' ? 'text-teal' : 'text-danger' }}">
                                        {{ $tx->type === 'credit' ? '+' : '-' }}₹{{ number_format($tx->amount, 2) }}
                                    </td>
                                    <td class="text-light small fw-semibold">₹{{ number_format($tx->balance_after, 2) }}</td>
                                    <td class="small text-secondary">{{ $tx->note }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-secondary small">No transactions logged for this customer.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>

</div>

<!-- =========================================================================
     MODAL A: ADMIN UPLOAD SONG FOR THIS CUSTOMER
     ========================================================================= -->
<div class="modal fade" id="adminAddSongModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(0,210,170,0.3); border-radius: 20px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light">
                    <i class="fa-solid fa-cloud-arrow-up text-teal me-2"></i> Upload Song for Customer: {{ $customer->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.customers.songs.store', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4 text-start">
                    
                    <div class="p-3 rounded-3 mb-4" style="background: #111a2e; border: 1px solid rgba(0,210,170,0.25);">
                        <div class="small text-white">
                            <i class="fa-solid fa-info-circle text-teal me-1"></i> Uploaded song and files will automatically link to <strong>{{ $customer->name }}</strong> and be visible inside both the customer's portal and this admin profile.
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-white">3000 &times; 3000 px Wallpaper / Cover Image <span class="text-danger">*</span></label>
                            <input type="file" name="cover_image" class="auth-input" style="padding-left: 14px;" accept="image/jpeg,image/png,image/jpg,image/webp" required>
                            <small class="text-teal d-block mt-1">Exact 3000 &times; 3000 px high resolution.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-white">MP3 Song File <span class="text-danger">*</span></label>
                            <input type="file" name="audio_file" class="auth-input" style="padding-left: 14px;" accept=".mp3,audio/mpeg,audio/mp3" required>
                            <small class="text-info d-block mt-1">High quality MP3 audio track.</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-white">Song Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" class="auth-input" style="padding-left: 14px;" placeholder="e.g. Dilbar Romance" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-white">Singer <span class="text-danger">*</span></label>
                            <input type="text" name="singer" class="auth-input" style="padding-left: 14px;" placeholder="e.g. Arijit Singh" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-white">Lyrics / Composer <span class="text-danger">*</span></label>
                            <input type="text" name="composer" class="auth-input" style="padding-left: 14px;" placeholder="e.g. Pritam" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-white">Producer <span class="text-danger">*</span></label>
                            <input type="text" name="producer" class="auth-input" style="padding-left: 14px;" placeholder="e.g. Rajdoot Nivedan Media" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-semibold text-white">Copyright &mdash; P-Line <span class="text-teal">(Auto-Included)</span></label>
                            <input type="text" name="copyright" class="auth-input" style="padding-left: 14px; background: rgba(0, 210, 170, 0.05); color: #00d2aa; font-weight: 600;" value="℗ 2026 Rajdoot Nivedan" readonly>
                            <small class="text-secondary mt-1 d-block" style="font-size: 0.72rem;">
                                Every newly uploaded track is automatically registered under: <strong class="text-light">℗ 2026 Rajdoot Nivedan</strong>
                            </small>
                        </div>
                    </div>

                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-dark-outline py-2 px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-hero-primary py-2 px-4 rounded-pill fw-bold">Upload &amp; Save Song</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL B: EDIT CUSTOMER INFO
     ========================================================================= -->
<div class="modal fade" id="editCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light">
                    <i class="fa-regular fa-pen-to-square text-teal me-2"></i> Edit Customer: {{ $customer->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.customers.update', $customer->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-white">Full Name</label>
                        <input type="text" name="name" class="auth-input" style="padding-left: 14px;" value="{{ $customer->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-white">Username</label>
                        <input type="text" name="username" class="auth-input" style="padding-left: 14px;" value="{{ $customer->username }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-white">Email Address</label>
                        <input type="email" name="email" class="auth-input" style="padding-left: 14px;" value="{{ $customer->email }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-white">Phone Number</label>
                        <input type="text" name="phone" class="auth-input" style="padding-left: 14px;" value="{{ $customer->phone }}">
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-white">Change Password (Optional)</label>
                        <input type="password" name="password" class="auth-input" style="padding-left: 14px;" placeholder="Leave empty to keep current password">
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-dark-outline py-2 px-3.5" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-hero-primary py-2 px-4 rounded-pill fw-bold">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- =========================================================================
     MODAL C: MANAGE EARNINGS (INCREASE / DECREASE / DIRECT EDIT)
     ========================================================================= -->
<div class="modal fade" id="manageEarningsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 20px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light">
                    <i class="fa-solid fa-coins text-teal me-2"></i> Update Earnings: {{ $customer->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                
                <div class="p-3 rounded-3 mb-4 d-flex justify-content-between flex-wrap gap-3" style="background: #151d30; border: 1px solid rgba(0,210,170,0.25);">
                    <div>
                        <div class="small text-secondary">Available Withdrawable Balance:</div>
                        <div class="fs-4 fw-extrabold text-teal">₹{{ number_format($customer->earning_balance, 2) }}</div>
                    </div>
                    <div>
                        <div class="small text-secondary">Total Lifetime Earnings:</div>
                        <div class="fs-4 fw-extrabold" style="color: #fbbf24;">₹{{ number_format($customer->total_earnings, 2) }}</div>
                    </div>
                    <div>
                        <div class="small text-secondary">Total Withdrawn:</div>
                        <div class="fs-4 fw-extrabold" style="color: #38bdf8;">₹{{ number_format($customer->withdrawn_amount, 2) }}</div>
                    </div>
                </div>

                <ul class="nav nav-pills gap-2 mb-3" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active btn-sm rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#increaseTab" type="button">
                            <i class="fa-solid fa-plus me-1"></i> Increase (+)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#decreaseTab" type="button">
                            <i class="fa-solid fa-minus me-1"></i> Decrease (-)
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#directTab" type="button">
                            <i class="fa-solid fa-sliders me-1"></i> Direct Edit
                        </button>
                    </li>
                </ul>

                <div class="tab-content pt-2">
                    <!-- INCREASE -->
                    <div class="tab-pane fade show active" id="increaseTab">
                        <form action="{{ route('admin.earnings.increase', $customer->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-white">Amount to Add (₹)</label>
                                <input type="number" step="0.01" min="0.01" name="amount" class="auth-input" style="padding-left: 14px; font-size: 1.1rem;" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-white">Reason / Description</label>
                                <input type="text" name="note" class="auth-input" style="padding-left: 14px;" value="Streaming royalty payout">
                            </div>
                            <button type="submit" class="btn btn-hero-primary">
                                <i class="fa-solid fa-plus me-1"></i> Increase Earnings
                            </button>
                        </form>
                    </div>

                    <!-- DECREASE -->
                    <div class="tab-pane fade" id="decreaseTab">
                        <form action="{{ route('admin.earnings.decrease', $customer->id) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-white">Amount to Deduct (₹)</label>
                                <input type="number" step="0.01" min="0.01" max="{{ $customer->earning_balance }}" name="amount" class="auth-input" style="padding-left: 14px; font-size: 1.1rem;" required>
                                <small class="text-secondary d-block mt-1">Maximum allowed: ₹{{ number_format($customer->earning_balance, 2) }}</small>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-white">Reason / Description</label>
                                <input type="text" name="note" class="auth-input" style="padding-left: 14px;" required>
                            </div>
                            <button type="submit" class="btn btn-hero-crimson rounded-pill fw-bold">
                                <i class="fa-solid fa-minus me-1"></i> Deduct Earnings
                            </button>
                        </form>
                    </div>

                    <!-- DIRECT EDIT -->
                    <div class="tab-pane fade" id="directTab">
                        <form action="{{ route('admin.earnings.update', $customer->id) }}" method="POST">
                            @csrf
                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-white">Set Available Balance (₹)</label>
                                    <input type="number" step="0.01" min="0" name="earning_balance" class="auth-input" style="padding-left: 14px;" value="{{ $customer->earning_balance }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold text-white">Set Total Lifetime Earnings (₹)</label>
                                    <input type="number" step="0.01" min="0" name="total_earnings" class="auth-input" style="padding-left: 14px;" value="{{ $customer->total_earnings }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-white">Reason / Note</label>
                                <input type="text" name="note" class="auth-input" style="padding-left: 14px;" value="Manual balance adjustment by Admin">
                            </div>
                            <button type="submit" class="btn btn-hero-primary">
                                <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@endsection
