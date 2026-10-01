@extends('layouts.app')

@section('title', 'Admin Console - Rajdoot Nivedan Media')

@section('content')
<div class="admin-page-wrapper">

    <!-- =========================================================================
         HEADER TITLE (Exact match to Reference Image)
         ========================================================================= -->
    <div class="mb-4">
        <h1 class="h2 fw-extrabold text-light mb-1" style="letter-spacing: -0.5px;">
            Admin Console
        </h1>
        <p class="mb-0 small" style="color: #94a3b8; font-size: 0.88rem;">
            Monitor customers, verifications and withdrawals.
        </p>
    </div>

    <!-- =========================================================================
         TOP 4 STAT CARDS ROW (Exact match to Reference Image)
         ========================================================================= -->
    <div class="row g-3 mb-3">
        
        <!-- 1. CUSTOMERS -->
        <div class="col-6 col-md-4 col-xl">
            <div class="exact-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; color: #94a3b8; text-transform: uppercase;">
                        CUSTOMERS
                    </span>
                    <i class="fa-solid fa-user-group text-teal" style="font-size: 1rem;"></i>
                </div>
                <div class="fs-2 fw-extrabold text-teal">
                    {{ $stats['total_customers'] }}
                </div>
            </div>
        </div>

        <!-- 2. UPLOADED SONGS -->
        <div class="col-6 col-md-4 col-xl">
            <div class="exact-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; color: #94a3b8; text-transform: uppercase;">
                        UPLOADED SONGS
                    </span>
                    <i class="fa-solid fa-compact-disc" style="color: #38bdf8; font-size: 1rem;"></i>
                </div>
                <div class="fs-2 fw-extrabold" style="color: #38bdf8;">
                    {{ $stats['total_songs'] }}
                </div>
            </div>
        </div>

        <!-- 3. PENDING KYC -->
        <div class="col-6 col-md-4 col-xl">
            <div class="exact-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; color: #94a3b8; text-transform: uppercase;">
                        PENDING KYC
                    </span>
                    <i class="fa-solid fa-shield-halved" style="color: #22d3ee; font-size: 1rem;"></i>
                </div>
                <div class="fs-2 fw-extrabold" style="color: #22d3ee;">
                    {{ $stats['pending_verifications'] }}
                </div>
            </div>
        </div>

        <!-- 4. PENDING WITHDRAWALS -->
        <div class="col-6 col-md-4 col-xl">
            <div class="exact-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; color: #94a3b8; text-transform: uppercase;">
                        PENDING WITHDRAWALS
                    </span>
                    <i class="fa-solid fa-wallet" style="color: #facc15; font-size: 1rem;"></i>
                </div>
                <div class="fs-2 fw-extrabold" style="color: #facc15;">
                    {{ $stats['pending_withdrawals'] }}
                </div>
            </div>
        </div>

        <!-- 5. TOTAL PAID OUT -->
        <div class="col-6 col-md-4 col-xl">
            <div class="exact-stat-card">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span style="font-size: 0.68rem; font-weight: 700; letter-spacing: 0.5px; color: #94a3b8; text-transform: uppercase;">
                        TOTAL PAID OUT
                    </span>
                    <i class="fa-solid fa-indian-rupee-sign" style="color: #c084fc; font-size: 1rem;"></i>
                </div>
                <div class="fs-2 fw-extrabold" style="color: #c084fc;">
                    ₹{{ number_format($stats['total_withdrawn_amount'], 0) }}
                </div>
            </div>
        </div>

    </div>

    <!-- =========================================================================
         CARD 1: WITHDRAWAL MANAGEMENT (Exact match to Reference Image)
         ========================================================================= -->
    <div class="exact-card mb-3">
        
        <!-- Header -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-regular fa-file-lines text-teal" style="font-size: 0.95rem;"></i>
            <h2 class="h6 fw-bold text-light mb-0" style="font-size: 0.92rem;">Withdrawal management</h2>
        </div>

        <!-- Filter Pills Row -->
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3 pt-1">
            <a href="{{ route('admin.dashboard') }}" class="filter-pill {{ empty($withdrawalStatus) ? 'active' : '' }}">
                All
            </a>
            <a href="{{ route('admin.dashboard', ['withdrawal_status' => 'pending']) }}" class="filter-pill {{ $withdrawalStatus === 'pending' ? 'active' : '' }}">
                Pending
            </a>
            <a href="{{ route('admin.dashboard', ['withdrawal_status' => 'approved']) }}" class="filter-pill {{ $withdrawalStatus === 'approved' ? 'active' : '' }}">
                Approved
            </a>
            <a href="{{ route('admin.dashboard', ['withdrawal_status' => 'rejected']) }}" class="filter-pill {{ $withdrawalStatus === 'rejected' ? 'active' : '' }}">
                Rejected
            </a>
        </div>

        <!-- Withdrawals Table -->
        <div class="table-responsive">
            <table class="table-custom-dark">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Amount</th>
                        <th>Requested</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allWithdrawals as $w)
                        <tr>
                            <!-- Customer -->
                            <td>
                                <div class="fw-bold text-light" style="font-size: 0.88rem;">{{ $w->user->username ?: $w->user->name }}</div>
                                <div class="small" style="color: #94a3b8; font-size: 0.78rem;">{{ $w->user->email }}</div>
                            </td>

                            <!-- Amount -->
                            <td>
                                <span class="fw-bold fs-6" style="color: #00d2aa;">₹{{ number_format($w->amount, 2) }}</span>
                            </td>

                            <!-- Requested Date -->
                            <td class="small" style="color: #94a3b8;">
                                {{ $w->created_at->format('n/j/Y') }}
                            </td>

                            <!-- Status Badge -->
                            <td>
                                @if($w->status === 'approved')
                                    <span class="badge-approved">approved</span>
                                @elseif($w->status === 'rejected')
                                    <span class="badge-rejected">rejected</span>
                                @else
                                    <span class="badge-pending">pending</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="text-end">
                                @if($w->status === 'pending')
                                    <div class="d-inline-flex gap-2">
                                        <form action="{{ route('admin.withdrawal.approve', $w->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-exact-teal py-1 px-3 small" onclick="return confirm('Approve & process payout of ₹{{ number_format($w->amount, 2) }}?');">
                                                <i class="fa-solid fa-check me-1"></i> Verify
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-sm btn-dark-outline py-1 px-2.5 small text-danger border-danger border-opacity-40" data-bs-toggle="modal" data-bs-target="#rejectWithdrawalModal{{ $w->id }}">
                                            <i class="fa-solid fa-xmark me-1"></i> Reject
                                        </button>
                                    </div>
                                @else
                                    <span class="small" style="color: #94a3b8;">Processed</span>
                                @endif
                            </td>
                        </tr>

                        <!-- Modal: Reject Withdrawal -->
                        @if($w->status === 'pending')
                            <div class="modal fade" id="rejectWithdrawalModal{{ $w->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                                        <div class="modal-header border-secondary border-opacity-25">
                                            <h5 class="modal-title fw-bold text-danger fs-6">Reject Withdrawal #WD-{{ $w->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.withdrawal.reject', $w->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <p class="small mb-3" style="color: #94a3b8;">Rejecting will automatically refund ₹{{ number_format($w->amount, 2) }} back to {{ $w->user->name }}'s balance.</p>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold" style="color: #94a3b8;">Rejection Reason</label>
                                                    <textarea name="admin_note" class="form-control exact-input" rows="3" required></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary border-opacity-25">
                                                <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger py-1.5 px-3 small rounded-3 fw-bold">Reject &amp; Refund</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color: #64748b; font-size: 0.86rem;">
                                No withdrawal records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- =========================================================================
         CARD 2: PROFILE VERIFICATIONS (Exact match to Reference Image)
         ========================================================================= -->
    <div class="exact-card mb-3">
        
        <!-- Header with Bell Counter Badge -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-shield-halved text-teal" style="font-size: 0.95rem;"></i>
            <h2 class="h6 fw-bold text-light mb-0" style="font-size: 0.92rem;">Profile verifications</h2>
            <span class="badge rounded-pill fw-bold text-dark px-2 py-0.5" style="background: #facc15; font-size: 0.72rem;">
                <i class="fa-solid fa-bell me-1"></i> {{ count($pendingVerifications) }}
            </span>
        </div>

        <!-- Pending Verifications List -->
        @forelse($pendingVerifications as $v)
            <div class="p-3.5 rounded-3 mb-2.5" style="background: #0c1220; border: 1px solid rgba(255, 255, 255, 0.06);">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    
                    <!-- Left: Avatar, Name, Email, and 2x2 Grid -->
                    <div class="d-flex align-items-start gap-3">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background: #00362c; border: 1.5px solid var(--teal); color: var(--teal); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; flex-shrink: 0;">
                            {{ strtoupper(substr($v->full_name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <span class="fw-bold text-light" style="font-size: 0.95rem;">{{ $v->full_name }}</span>
                                <span class="small" style="color: #94a3b8; font-size: 0.8rem;">{{ $v->email }}</span>
                            </div>
                            <div class="row g-x-4 g-y-1 small" style="font-size: 0.78rem;">
                                <div class="col-sm-6">
                                    <span style="color: #94a3b8;">PAN:</span>
                                    <span class="fw-semibold text-light ms-1">{{ $v->pan_number }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span style="color: #94a3b8;">Phone:</span>
                                    <span class="fw-semibold text-light ms-1">{{ $v->phone }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span style="color: #94a3b8;">Bank A/C:</span>
                                    <span class="fw-semibold text-light ms-1">{{ $v->bank_account }}</span>
                                </div>
                                <div class="col-sm-6">
                                    <span style="color: #94a3b8;">IFSC:</span>
                                    <span class="fw-semibold text-light ms-1">{{ $v->ifsc_code }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right / Bottom: Action Buttons (Signature, Verify, Reject) -->
                    <div class="d-flex align-items-center gap-2 pt-2 pt-md-0 ms-md-auto flex-shrink-0">
                        <!-- View Verification Documents Modal Trigger -->
                        <button type="button" class="btn btn-sm btn-dark-outline py-1.5 px-3 rounded-pill small" data-bs-toggle="modal" data-bs-target="#viewDocModal{{ $v->id }}">
                            <i class="fa-regular fa-id-card me-1 text-teal"></i> View KYC Docs
                        </button>

                        <!-- Verify Button -->
                        <form action="{{ route('admin.verification.approve', $v->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-exact-teal py-1.5 px-3 rounded-pill small">
                                <i class="fa-solid fa-check me-1"></i> Verify
                            </button>
                        </form>

                        <!-- Reject Button -->
                        <button type="button" class="btn btn-sm btn-dark-outline py-1.5 px-3 rounded-pill small text-danger border-danger border-opacity-40" data-bs-toggle="modal" data-bs-target="#rejectVerModal{{ $v->id }}">
                            <i class="fa-solid fa-xmark me-1"></i> Reject
                        </button>
                    </div>

                </div>
            </div>

            <!-- Modal: View Verification Documents -->
            <div class="modal fade" id="viewDocModal{{ $v->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(0,210,170,0.3); border-radius: 16px;">
                        <div class="modal-header border-secondary border-opacity-25">
                            <h5 class="modal-title fw-bold text-light fs-6">KYC Documents: {{ $v->full_name }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 text-center">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="fw-bold small text-teal mb-2">PAN Card Image</div>
                                    <div class="p-2 rounded-3 border border-secondary border-opacity-25" style="background: #070b14;">
                                        @if($v->pan_card_photo_url)
                                            <a href="{{ $v->pan_card_photo_url }}" target="_blank" title="Click to view full PAN Card">
                                                <img src="{{ $v->pan_card_photo_url }}" alt="PAN Card: {{ $v->full_name }}" class="img-fluid rounded" style="max-height: 260px; width: 100%; object-fit: contain;">
                                            </a>
                                            <div class="mt-2.5 d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                                <a href="{{ route('admin.verification.download_pan', $v->id) }}" class="btn btn-sm btn-exact-teal py-1.5 px-3 rounded-pill fw-bold">
                                                    <i class="fa-solid fa-download me-1"></i> Download PAN
                                                </a>
                                                <a href="{{ $v->pan_card_photo_url }}" target="_blank" class="btn btn-sm btn-dark-outline py-1.5 px-2.5 rounded-pill small text-teal">
                                                    <i class="fa-solid fa-up-right-from-square me-1"></i> Open
                                                </a>
                                            </div>
                                        @else
                                            <div class="py-5 text-secondary small">
                                                <i class="fa-regular fa-id-card fs-1 d-block mb-2 text-teal opacity-50"></i>
                                                No PAN card uploaded
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fw-bold small text-info mb-2">Signature Photo</div>
                                    <div class="p-2 rounded-3 border border-secondary border-opacity-25" style="background: #070b14;">
                                        @if($v->signature_photo_url)
                                            <a href="{{ $v->signature_photo_url }}" target="_blank" title="Click to view full Signature">
                                                <img src="{{ $v->signature_photo_url }}" alt="Signature: {{ $v->full_name }}" class="img-fluid rounded" style="max-height: 260px; width: 100%; object-fit: contain;">
                                            </a>
                                            <div class="mt-2.5 d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                                <a href="{{ route('admin.verification.download_signature', $v->id) }}" class="btn btn-sm btn-exact-teal py-1.5 px-3 rounded-pill fw-bold" style="background: linear-gradient(135deg, #0284c7, #0ea5e9); border-color: #38bdf8;">
                                                    <i class="fa-solid fa-download me-1"></i> Download Signature
                                                </a>
                                                <a href="{{ $v->signature_photo_url }}" target="_blank" class="btn btn-sm btn-dark-outline py-1.5 px-2.5 rounded-pill small text-info">
                                                    <i class="fa-solid fa-up-right-from-square me-1"></i> Open
                                                </a>
                                            </div>
                                        @else
                                            <div class="py-5 text-secondary small">
                                                <i class="fa-solid fa-signature fs-1 d-block mb-2 text-info opacity-50"></i>
                                                No signature uploaded
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-secondary border-opacity-25">
                            <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal: Reject Verification -->
            <div class="modal fade" id="rejectVerModal{{ $v->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                        <div class="modal-header border-secondary border-opacity-25">
                            <h5 class="modal-title fw-bold text-danger fs-6">Reject Verification: {{ $v->full_name }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <form action="{{ route('admin.verification.reject', $v->id) }}" method="POST">
                            @csrf
                            <div class="modal-body p-4">
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold" style="color: #94a3b8;">Rejection Reason</label>
                                    <textarea name="rejection_reason" class="form-control exact-input" rows="3" required placeholder="State reason for rejection..."></textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-secondary border-opacity-25">
                                <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger py-1.5 px-3 small rounded-3 fw-bold">Reject Verification</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-4" style="color: #64748b; font-size: 0.86rem;">
                No pending verifications awaiting review.
            </div>
        @endforelse

    </div>

    <!-- =========================================================================
         CARD 3: REGISTERED CUSTOMERS (Exact match to Reference Image)
         ========================================================================= -->
    <div class="exact-card mb-3">
        <!-- Header with Bell Badge (Exact match to Reference Image) -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-users text-teal" style="font-size: 0.95rem;"></i>
            <h2 class="h6 fw-bold text-light mb-0" style="font-size: 0.92rem;">Registered customers</h2>
            <span class="badge rounded-pill fw-bold text-dark px-2 py-0.5" style="background: #facc15; font-size: 0.72rem;">
                <i class="fa-solid fa-bell me-1"></i> {{ $users->total() }}
            </span>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table-custom-dark">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Earnings</th>
                        <th>KYC</th>
                        <th>Withdrawn</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <!-- Customer: Avatar, Name, Email -->
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div style="width: 36px; height: 36px; border-radius: 50%; background: #00362c; border: 1.5px solid var(--teal); color: var(--teal); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.82rem; flex-shrink: 0;">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-light" style="font-size: 0.88rem;">{{ $user->name }}</div>
                                        <div class="small" style="color: #94a3b8; font-size: 0.78rem;">{{ $user->email }}</div>
                                        <div class="mt-1 d-flex align-items-center gap-2 flex-wrap" style="font-size: 0.75rem;">
                                            @if($user->autocart_generator_enabled)
                                                <span class="fw-bold text-success" style="color: #10b981 !important;">
                                                    <i class="fa-solid fa-circle" style="font-size: 6px; vertical-align: middle; margin-right: 3px;"></i> Autocart Generator is ON
                                                </span>
                                            @else
                                                <span class="fw-bold text-danger" style="color: #ef4444 !important;">
                                                    <i class="fa-solid fa-circle" style="font-size: 6px; vertical-align: middle; margin-right: 3px;"></i> Autocart Generator is OFF
                                                </span>
                                            @endif
                                            <span class="badge px-2 py-0.5 rounded-pill" style="background: rgba(0, 210, 170, 0.12); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.25); font-size: 0.72rem;">
                                                <i class="fa-solid fa-compact-disc me-1"></i> {{ $user->songs->count() }} {{ $user->songs->count() === 1 ? 'Track' : 'Tracks' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Earnings -->
                            <td>
                                <span class="fw-bold fs-6" style="color: #00d2aa;">
                                    ₹{{ number_format($user->earning_balance, 2) }}
                                </span>
                            </td>

                            <!-- KYC Badge (Clickable to view & download KYC documents) -->
                            <td>
                                @if($user->isVerified())
                                    <span class="badge-approved" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#adminCustomerKycModal{{ $user->id }}" title="Click to view & download verified KYC documents">
                                        <i class="fa-solid fa-shield-check me-0.5"></i> verified
                                    </span>
                                @elseif($user->verification && $user->verification->status === 'pending')
                                    <span class="badge-pending" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#adminCustomerKycModal{{ $user->id }}" title="Click to review & download pending KYC documents">
                                        <i class="fa-solid fa-clock me-0.5"></i> pending
                                    </span>
                                @elseif($user->verification && $user->verification->status === 'rejected')
                                    <span class="badge-rejected" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#adminCustomerKycModal{{ $user->id }}" title="Click to view rejected KYC details">
                                        <i class="fa-solid fa-circle-xmark me-0.5"></i> rejected
                                    </span>
                                @else
                                    <span class="badge-unverified" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#adminCustomerKycModal{{ $user->id }}" title="Click to check KYC status">
                                        unverified
                                    </span>
                                @endif
                            </td>

                            <!-- Withdrawn -->
                            <td class="small" style="color: #94a3b8; font-size: 0.88rem;">
                                ₹{{ number_format($user->withdrawn_amount, 0) }}
                            </td>

                            <!-- Actions: [✏️ Earnings] [🪪 Profile Info] [🛡️ KYC Docs] [👁️ View] -->
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <!-- Earnings Modal Trigger -->
                                    <button type="button" class="admin-action-link" data-bs-toggle="modal" data-bs-target="#manageEarningsModal{{ $user->id }}" title="Manage Earnings for {{ $user->name }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Earnings
                                    </button>

                                    <!-- View Profile Info Modal Trigger -->
                                    <button type="button" class="admin-action-link" data-bs-toggle="modal" data-bs-target="#viewProfileInfoModal{{ $user->id }}" title="View Profile Info for {{ $user->name }}">
                                        <i class="fa-solid fa-id-card"></i> Profile Info
                                        @if($user->isProfileComplete())
                                            <span class="profile-status-dot dot-green ms-1" title="Profile Complete (Green)"></span>
                                        @else
                                            <span class="profile-status-dot dot-red ms-1" title="Profile Incomplete (Red)"></span>
                                        @endif
                                    </button>

                                    <!-- View Customer KYC Documents Modal Trigger -->
                                    <button type="button" class="admin-action-link" data-bs-toggle="modal" data-bs-target="#adminCustomerKycModal{{ $user->id }}" title="View & Download KYC Documents (PAN & Signature) for {{ $user->name }}">
                                        <i class="fa-solid fa-shield-halved text-teal"></i> KYC Docs
                                        @if($user->verification && ($user->verification->pan_card_photo || $user->verification->signature_photo))
                                            <span class="badge bg-success rounded-pill ms-0.5 p-1" style="width: 7px; height: 7px; display: inline-block; vertical-align: middle;"></span>
                                        @endif
                                    </button>

                                    <!-- View Individual Customer Profile Link -->
                                    <a href="{{ route('admin.customers.show', $user->id) }}" class="admin-action-link" title="Open full customer profile & manage songs">
                                        <i class="fa-regular fa-eye"></i> View Profile
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal: Manage Customer Earnings -->
                        <div class="modal fade" id="manageEarningsModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                                    <div class="modal-header border-secondary border-opacity-25">
                                        <h5 class="modal-title fw-bold text-light fs-6">
                                            <i class="fa-solid fa-coins text-teal me-2"></i> Update Earnings: {{ $user->name }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        
                                        <!-- Customer Balance Overview -->
                                        <div class="p-3 rounded-3 mb-3 d-flex justify-content-between flex-wrap gap-3" style="background: #111726; border: 1px solid rgba(0,210,170,0.25);">
                                            <div>
                                                <div class="small" style="color: #94a3b8;">Available Balance:</div>
                                                <div class="fs-5 fw-extrabold text-teal">₹{{ number_format($user->earning_balance, 2) }}</div>
                                            </div>
                                            <div>
                                                <div class="small" style="color: #94a3b8;">Total Lifetime:</div>
                                                <div class="fs-5 fw-extrabold" style="color: #facc15;">₹{{ number_format($user->total_earnings, 2) }}</div>
                                            </div>
                                            <div>
                                                <div class="small" style="color: #94a3b8;">Total Withdrawn:</div>
                                                <div class="fs-5 fw-extrabold" style="color: #22d3ee;">₹{{ number_format($user->withdrawn_amount, 2) }}</div>
                                            </div>
                                        </div>

                                        <!-- Tabs for Increase / Decrease / Direct Edit -->
                                        <ul class="nav nav-pills gap-2 mb-3" role="tablist">
                                            <li class="nav-item">
                                                <button class="nav-link active btn-sm rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#increaseTab{{ $user->id }}" type="button">
                                                    <i class="fa-solid fa-plus me-1"></i> Increase (+)
                                                </button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link btn-sm rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#decreaseTab{{ $user->id }}" type="button">
                                                    <i class="fa-solid fa-minus me-1"></i> Decrease (-)
                                                </button>
                                            </li>
                                            <li class="nav-item">
                                                <button class="nav-link btn-sm rounded-pill fw-bold" data-bs-toggle="pill" data-bs-target="#directTab{{ $user->id }}" type="button">
                                                    <i class="fa-solid fa-sliders me-1"></i> Direct Edit
                                                </button>
                                            </li>
                                        </ul>

                                        <div class="tab-content pt-2">
                                            <!-- INCREASE TAB -->
                                            <div class="tab-pane fade show active" id="increaseTab{{ $user->id }}">
                                                <form action="{{ route('admin.earnings.increase', $user->id) }}" method="POST">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Amount to Add (₹)</label>
                                                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control exact-input" style="font-size: 1.1rem; font-weight: 700;" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Reason / Description</label>
                                                        <input type="text" name="note" class="form-control exact-input" value="Streaming royalty payout">
                                                    </div>
                                                    <button type="submit" class="btn btn-exact-teal py-1.5 px-3">
                                                        <i class="fa-solid fa-plus me-1"></i> Increase Earnings
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- DECREASE TAB -->
                                            <div class="tab-pane fade" id="decreaseTab{{ $user->id }}">
                                                <form action="{{ route('admin.earnings.decrease', $user->id) }}" method="POST">
                                                    @csrf
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Amount to Deduct (₹)</label>
                                                        <input type="number" step="0.01" min="0.01" max="{{ $user->earning_balance }}" name="amount" class="form-control exact-input" style="font-size: 1.1rem; font-weight: 700;" required>
                                                        <small class="d-block mt-1" style="color: #64748b;">Maximum allowed: ₹{{ number_format($user->earning_balance, 2) }}</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Reason / Description</label>
                                                        <input type="text" name="note" class="form-control exact-input" required>
                                                    </div>
                                                    <button type="submit" class="btn btn-danger rounded-pill fw-bold py-1.5 px-3 small">
                                                        <i class="fa-solid fa-minus me-1"></i> Deduct Earnings
                                                    </button>
                                                </form>
                                            </div>

                                            <!-- DIRECT EDIT TAB -->
                                            <div class="tab-pane fade" id="directTab{{ $user->id }}">
                                                <form action="{{ route('admin.earnings.update', $user->id) }}" method="POST">
                                                    @csrf
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Set Available Balance (₹)</label>
                                                            <input type="number" step="0.01" min="0" name="earning_balance" class="form-control exact-input" value="{{ $user->earning_balance }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Set Total Lifetime Earnings (₹)</label>
                                                            <input type="number" step="0.01" min="0" name="total_earnings" class="form-control exact-input" value="{{ $user->total_earnings }}" required>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Reason / Note</label>
                                                        <input type="text" name="note" class="form-control exact-input" value="Manual balance adjustment by Admin">
                                                    </div>
                                                    <button type="submit" class="btn btn-exact-teal py-1.5 px-3">
                                                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Changes
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer border-secondary border-opacity-25">
                                        <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal: View Customer Profile Info (Dedicated per customer) -->
                        <div class="modal fade" id="viewProfileInfoModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-md">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.12); border-radius: 18px; box-shadow: 0 25px 50px rgba(0,0,0,0.8);">
                                    <div class="modal-header border-secondary border-opacity-25 px-4 pt-3.5 pb-3">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #00362c; border: 1.5px solid var(--teal); color: var(--teal); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-light fs-6 mb-0">Profile Info: {{ $user->name }}</h5>
                                                <div class="small" style="color: #94a3b8; font-size: 0.76rem;">Customer ID: #CUST-{{ $user->id }} &bull; {{ $user->email }}</div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body p-4">
                                        <!-- Profile Status Header Banner -->
                                        <div class="p-3 rounded-3 mb-3 d-flex align-items-center justify-content-between" style="background: #111726; border: 1px solid {{ $user->isProfileComplete() ? 'rgba(16, 185, 129, 0.35)' : 'rgba(239, 68, 68, 0.35)' }};">
                                            <div>
                                                <div class="small text-secondary" style="font-size: 0.78rem;">Status:</div>
                                                <div class="fw-bold {{ $user->isProfileComplete() ? 'text-teal' : 'text-danger' }}" style="font-size: 0.92rem;">
                                                    {{ $user->isProfileComplete() ? 'All 4 Details Completed' : 'Profile Incomplete' }}
                                                </div>
                                            </div>
                                            <span class="badge-profile-status {{ $user->isProfileComplete() ? 'complete' : 'incomplete' }}">
                                                <i class="fa-solid {{ $user->isProfileComplete() ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                                                {{ $user->isProfileComplete() ? 'Complete' : 'Incomplete' }}
                                            </span>
                                        </div>

                                        <!-- Profile Details Grid -->
                                        <div class="d-flex flex-column gap-2.5">
                                            <!-- 1. Owner Name -->
                                            <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.07);">
                                                <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                    <i class="fa-solid fa-user text-teal me-1.5"></i> Owner Name
                                                </div>
                                                <div class="text-light fw-bold fs-6">
                                                    {{ $user->profileInfo->owner_name ?? '— Not submitted yet —' }}
                                                </div>
                                            </div>

                                            <!-- 2. YouTube Channel Name -->
                                            <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.07);">
                                                <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                    <i class="fa-brands fa-youtube text-danger me-1.5"></i> YouTube Channel Name
                                                </div>
                                                <div class="text-light fw-bold fs-6">
                                                    {{ $user->profileInfo->channel_name ?? '— Not submitted yet —' }}
                                                </div>
                                            </div>

                                            <!-- 3. YouTube Link -->
                                            <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.07);">
                                                <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                    <i class="fa-solid fa-link text-info me-1.5"></i> YouTube Link
                                                </div>
                                                <div>
                                                    @if(!empty($user->profileInfo->youtube_link))
                                                        <a href="{{ $user->profileInfo->youtube_link }}" target="_blank" rel="noopener noreferrer" class="text-teal text-decoration-none fw-bold small d-inline-flex align-items-center gap-1.5" style="word-break: break-all;">
                                                            <span>{{ $user->profileInfo->youtube_link }}</span>
                                                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 0.75rem;"></i>
                                                        </a>
                                                    @else
                                                        <span class="text-secondary small">— Not submitted yet —</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- 4. Label Name -->
                                            <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.07);">
                                                <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                    <i class="fa-solid fa-tag text-warning me-1.5"></i> Label Name
                                                </div>
                                                <div class="text-light fw-bold fs-6">
                                                    {{ $user->profileInfo->label_name ?? '— Not submitted yet —' }}
                                                </div>
                                            </div>

                                            <!-- 5. Autocart Generator -->
                                            <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.07);">
                                                <div class="small text-secondary text-uppercase fw-semibold mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                    <i class="fa-solid fa-wand-magic-sparkles text-teal me-1.5"></i> Autocart Generator
                                                </div>
                                                <div class="fs-6 fw-bold">
                                                    @if($user->autocart_generator_enabled)
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

                                            <!-- 6. Uploaded Verification Documents (PAN Card & Signature) -->
                                            <div class="p-3 rounded-3" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.07);">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <div class="small text-secondary text-uppercase fw-semibold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                                                        <i class="fa-solid fa-shield-halved text-teal me-1.5"></i> Uploaded Verification Documents
                                                    </div>
                                                    @if($user->verification)
                                                        <span class="badge {{ $user->verification->status === 'approved' ? 'badge-approved' : ($user->verification->status === 'rejected' ? 'badge-rejected' : 'badge-pending') }} py-0.5 px-2 text-uppercase" style="font-size: 0.68rem;">
                                                            {{ $user->verification->status }}
                                                        </span>
                                                    @else
                                                        <span class="badge badge-unverified py-0.5 px-2 text-uppercase" style="font-size: 0.68rem;">
                                                            Unverified
                                                        </span>
                                                    @endif
                                                </div>

                                                @if($user->verification && ($user->verification->pan_card_photo_url || $user->verification->signature_photo_url))
                                                    <div class="row g-2 mt-1">
                                                        <!-- PAN Card Image -->
                                                        <div class="col-6 text-center">
                                                            <div class="small fw-semibold text-secondary mb-1" style="font-size: 0.74rem;">PAN Card Image</div>
                                                            <div class="p-1.5 rounded-3 border border-secondary border-opacity-25" style="background: #070b14;">
                                                                @if($user->verification->pan_card_photo_url)
                                                                    <a href="{{ $user->verification->pan_card_photo_url }}" target="_blank" title="Click to view full PAN Card">
                                                                        <img src="{{ $user->verification->pan_card_photo_url }}" alt="PAN Card: {{ $user->name }}" class="img-fluid rounded" style="max-height: 120px; width: 100%; object-fit: contain;">
                                                                    </a>
                                                                    <div class="mt-1.5 d-flex align-items-center justify-content-center gap-1.5 flex-wrap">
                                                                        <a href="{{ route('admin.verification.download_pan', $user->verification->id) }}" class="btn btn-sm btn-exact-teal py-0.5 px-2 rounded-pill fw-bold" style="font-size: 0.7rem;" title="Download PAN Card">
                                                                            <i class="fa-solid fa-download me-0.5"></i> Download
                                                                        </a>
                                                                        <a href="{{ $user->verification->pan_card_photo_url }}" target="_blank" class="btn btn-sm btn-dark-outline py-0.5 px-1.5 rounded-pill small text-teal" style="font-size: 0.7rem;" title="Open in new tab">
                                                                            <i class="fa-solid fa-up-right-from-square"></i>
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <span class="text-secondary small d-block py-3" style="font-size: 0.75rem;">Not uploaded</span>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Signature Photo -->
                                                        <div class="col-6 text-center">
                                                            <div class="small fw-semibold text-secondary mb-1" style="font-size: 0.74rem;">Signature Photo</div>
                                                            <div class="p-1.5 rounded-3 border border-secondary border-opacity-25" style="background: #070b14;">
                                                                @if($user->verification->signature_photo_url)
                                                                    <a href="{{ $user->verification->signature_photo_url }}" target="_blank" title="Click to view full Signature">
                                                                        <img src="{{ $user->verification->signature_photo_url }}" alt="Signature: {{ $user->name }}" class="img-fluid rounded" style="max-height: 120px; width: 100%; object-fit: contain;">
                                                                    </a>
                                                                    <div class="mt-1.5 d-flex align-items-center justify-content-center gap-1.5 flex-wrap">
                                                                        <a href="{{ route('admin.verification.download_signature', $user->verification->id) }}" class="btn btn-sm btn-exact-teal py-0.5 px-2 rounded-pill fw-bold" style="background: linear-gradient(135deg, #0284c7, #0ea5e9); border-color: #38bdf8; font-size: 0.7rem;" title="Download Signature">
                                                                            <i class="fa-solid fa-download me-0.5"></i> Download
                                                                        </a>
                                                                        <a href="{{ $user->verification->signature_photo_url }}" target="_blank" class="btn btn-sm btn-dark-outline py-0.5 px-1.5 rounded-pill small text-info" style="font-size: 0.7rem;" title="Open in new tab">
                                                                            <i class="fa-solid fa-up-right-from-square"></i>
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <span class="text-secondary small d-block py-3" style="font-size: 0.75rem;">Not uploaded</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="text-secondary small py-2 text-center" style="font-size: 0.8rem;">
                                                        <i class="fa-regular fa-folder-open me-1 opacity-50"></i> No verification documents uploaded yet.
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        @if($user->profileInfo)
                                            <div class="mt-3 text-end small text-secondary" style="font-size: 0.75rem;">
                                                Last updated: {{ $user->profileInfo->updated_at->format('M d, Y h:i A') }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer border-secondary border-opacity-25 px-4 py-2.5 d-flex justify-content-between">
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.customers.show', $user->id) }}" class="btn btn-sm btn-dark-outline rounded-pill py-1.5 px-3">
                                                <i class="fa-regular fa-eye me-1"></i> Full Customer Profile
                                            </a>
                                            <button type="button" class="btn btn-sm btn-exact-teal py-1.5 px-3 rounded-pill" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#adminCustomerKycModal{{ $user->id }}">
                                                <i class="fa-solid fa-shield-halved me-1"></i> View KYC Docs
                                            </button>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-dark-outline rounded-pill py-1.5 px-3.5" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal: Dedicated Customer KYC Documents (PAN Card & Signature) Viewer & Downloader -->
                        <div class="modal fade" id="adminCustomerKycModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(0,210,170,0.35); border-radius: 18px; box-shadow: 0 20px 40px rgba(0,0,0,0.6);">
                                    <div class="modal-header border-secondary border-opacity-25 px-4 py-3">
                                        <div class="d-flex align-items-center gap-2.5">
                                            <div style="width: 40px; height: 40px; border-radius: 50%; background: #00362c; border: 1.5px solid var(--teal); color: var(--teal); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.95rem; flex-shrink: 0;">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <h5 class="modal-title fw-bold text-light fs-6 mb-0 d-flex align-items-center gap-2">
                                                    <span>{{ $user->name }}</span>
                                                    <span class="text-secondary small fw-normal">#CUST-{{ $user->id }}</span>
                                                </h5>
                                                <span class="text-secondary" style="font-size: 0.78rem;">{{ $user->email }}</span>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($user->isVerified())
                                                <span class="badge-approved"><i class="fa-solid fa-circle-check me-1"></i> verified</span>
                                            @elseif($user->verification && $user->verification->status === 'pending')
                                                <span class="badge-pending"><i class="fa-solid fa-clock me-1"></i> pending review</span>
                                            @elseif($user->verification && $user->verification->status === 'rejected')
                                                <span class="badge-rejected"><i class="fa-solid fa-circle-xmark me-1"></i> rejected</span>
                                            @else
                                                <span class="badge-unverified">unverified</span>
                                            @endif
                                            <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
                                        </div>
                                    </div>
                                    <div class="modal-body p-4">
                                        @if($user->verification)
                                            <!-- Verification Summary Banner -->
                                            <div class="p-3 rounded-3 mb-3.5" style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08);">
                                                <div class="row g-2 small" style="font-size: 0.8rem;">
                                                    <div class="col-sm-6">
                                                        <span class="text-secondary">Full Legal Name:</span>
                                                        <strong class="text-light ms-1">{{ $user->verification->full_name }}</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-secondary">PAN Number:</span>
                                                        <strong class="text-teal font-monospace ms-1">{{ $user->verification->pan_number }}</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-secondary">Bank Account:</span>
                                                        <strong class="text-light font-monospace ms-1">{{ $user->verification->bank_account }}</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-secondary">IFSC Code:</span>
                                                        <strong class="text-info font-monospace ms-1">{{ $user->verification->ifsc_code }}</strong>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-secondary">Phone:</span>
                                                        <span class="text-light ms-1">{{ $user->verification->phone }}</span>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <span class="text-secondary">Submitted:</span>
                                                        <span class="text-light ms-1">{{ $user->verification->created_at ? $user->verification->created_at->format('M d, Y') : 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Document Photos & Direct Downloads -->
                                            <div class="row g-3">
                                                <!-- PAN Card Card -->
                                                <div class="col-md-6 text-center">
                                                    <div class="p-3 rounded-3 h-100 border border-secondary border-opacity-25" style="background: #070b14;">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="fw-bold small text-teal">
                                                                <i class="fa-regular fa-id-card me-1"></i> PAN Card Document
                                                            </span>
                                                            @if($user->verification->pan_card_photo_url)
                                                                <span class="badge py-0.5 px-2" style="background: rgba(0, 210, 170, 0.15); color: #00d2aa; font-size: 0.68rem;">Ready</span>
                                                            @endif
                                                        </div>
                                                        @if($user->verification->pan_card_photo_url)
                                                            <div class="position-relative overflow-hidden rounded-2 mb-2" style="background: #020617; border: 1px solid rgba(255,255,255,0.06);">
                                                                <a href="{{ $user->verification->pan_card_photo_url }}" target="_blank" title="Click to view full PAN Card">
                                                                    <img src="{{ $user->verification->pan_card_photo_url }}" alt="PAN Card: {{ $user->name }}" class="img-fluid" style="max-height: 220px; width: 100%; object-fit: contain;">
                                                                </a>
                                                            </div>
                                                            <div class="mt-2.5 d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                                                <a href="{{ route('admin.verification.download_pan', $user->verification->id) }}" class="btn btn-sm btn-exact-teal py-1.5 px-3 rounded-pill fw-bold" title="Download {{ $user->name }}'s PAN Card">
                                                                    <i class="fa-solid fa-download me-1"></i> Download PAN Card
                                                                </a>
                                                                <a href="{{ $user->verification->pan_card_photo_url }}" target="_blank" class="btn btn-sm btn-dark-outline py-1.5 px-2.5 rounded-pill small text-teal" title="Open PAN Card in new tab">
                                                                    <i class="fa-solid fa-up-right-from-square me-1"></i> Open
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="py-5 text-secondary small">
                                                                <i class="fa-regular fa-id-card fs-1 d-block mb-2 text-teal opacity-40"></i>
                                                                No PAN card document uploaded
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Signature Card -->
                                                <div class="col-md-6 text-center">
                                                    <div class="p-3 rounded-3 h-100 border border-secondary border-opacity-25" style="background: #070b14;">
                                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                                            <span class="fw-bold small text-info">
                                                                <i class="fa-solid fa-signature me-1"></i> Customer Signature
                                                            </span>
                                                            @if($user->verification->signature_photo_url)
                                                                <span class="badge py-0.5 px-2" style="background: rgba(14, 165, 233, 0.15); color: #38bdf8; font-size: 0.68rem;">Ready</span>
                                                            @endif
                                                        </div>
                                                        @if($user->verification->signature_photo_url)
                                                            <div class="position-relative overflow-hidden rounded-2 mb-2" style="background: #020617; border: 1px solid rgba(255,255,255,0.06);">
                                                                <a href="{{ $user->verification->signature_photo_url }}" target="_blank" title="Click to view full Signature">
                                                                    <img src="{{ $user->verification->signature_photo_url }}" alt="Signature: {{ $user->name }}" class="img-fluid" style="max-height: 220px; width: 100%; object-fit: contain;">
                                                                </a>
                                                            </div>
                                                            <div class="mt-2.5 d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                                                <a href="{{ route('admin.verification.download_signature', $user->verification->id) }}" class="btn btn-sm btn-exact-teal py-1.5 px-3 rounded-pill fw-bold" style="background: linear-gradient(135deg, #0284c7, #0ea5e9); border-color: #38bdf8;" title="Download {{ $user->name }}'s Signature">
                                                                    <i class="fa-solid fa-download me-1"></i> Download Signature
                                                                </a>
                                                                <a href="{{ $user->verification->signature_photo_url }}" target="_blank" class="btn btn-sm btn-dark-outline py-1.5 px-2.5 rounded-pill small text-info" title="Open Signature in new tab">
                                                                    <i class="fa-solid fa-up-right-from-square me-1"></i> Open
                                                                </a>
                                                            </div>
                                                        @else
                                                            <div class="py-5 text-secondary small">
                                                                <i class="fa-solid fa-signature fs-1 d-block mb-2 text-info opacity-40"></i>
                                                                No signature photo uploaded
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>

                                            @if($user->verification->status === 'pending')
                                                <!-- Quick Review Actions if Pending -->
                                                <div class="mt-4 pt-3 border-top border-secondary border-opacity-25 d-flex gap-2 justify-content-end align-items-center">
                                                    <span class="text-secondary small me-auto">This verification is awaiting your review:</span>
                                                    <form action="{{ route('admin.verification.approve', $user->verification->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-exact-teal py-1.5 px-3.5 rounded-pill fw-bold">
                                                            <i class="fa-solid fa-check me-1"></i> Approve KYC
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-danger py-1.5 px-3.5 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#rejectCustomerVerModal{{ $user->id }}">
                                                        <i class="fa-solid fa-xmark me-1"></i> Reject
                                                    </button>
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center py-5">
                                                <i class="fa-solid fa-shield-slash fs-1 text-secondary opacity-40 mb-3 d-block"></i>
                                                <h6 class="text-light fw-bold">No KYC Verification Documents Submitted Yet</h6>
                                                <p class="text-secondary small mb-3">This customer ({{ $user->name }}) has not submitted their PAN card or signature documents yet.</p>
                                                <a href="{{ route('admin.customers.show', $user->id) }}" class="btn btn-sm btn-dark-outline rounded-pill py-1.5 px-3">
                                                    <i class="fa-regular fa-eye me-1"></i> View Customer Profile
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="modal-footer border-secondary border-opacity-25 px-4 py-2.5 d-flex justify-content-between">
                                        <a href="{{ route('admin.customers.show', $user->id) }}" class="btn btn-sm btn-dark-outline rounded-pill py-1.5 px-3">
                                            <i class="fa-regular fa-eye me-1"></i> Full Customer Profile
                                        </a>
                                        <button type="button" class="btn btn-sm btn-dark-outline rounded-pill py-1.5 px-3.5" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($user->verification && $user->verification->status === 'pending')
                            <!-- Modal: Quick Reject Verification from Customer List -->
                            <div class="modal fade" id="rejectCustomerVerModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                                        <div class="modal-header border-secondary border-opacity-25">
                                            <h5 class="modal-title fw-bold text-danger fs-6">Reject Verification: {{ $user->name }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.verification.reject', $user->verification->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold" style="color: #94a3b8;">Rejection Reason</label>
                                                    <textarea name="rejection_reason" class="form-control exact-input" rows="3" required placeholder="State reason for rejection..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-secondary border-opacity-25">
                                                <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger py-1.5 px-3 small rounded-3 fw-bold">Reject Verification</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color: #64748b; font-size: 0.86rem;">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- =========================================================================
         CARD 4: CUSTOMER UPLOADED SONGS & AUDIO CATALOG (3000 × 3000 PX & MP3)
         ========================================================================= -->
    <div class="exact-card mb-4" id="adminUploadedSongsSection">
        
        <!-- Header with Count Badge and Search Form -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-compact-disc text-teal" style="font-size: 1rem;"></i>
                <h2 class="h6 fw-bold text-light mb-0" style="font-size: 0.95rem;">Customer uploaded songs &amp; audio catalog</h2>
                <span class="badge rounded-pill fw-bold px-2.5 py-0.5" style="background: rgba(0, 210, 170, 0.2); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.4); font-size: 0.72rem;">
                    {{ $allSongs->total() }} Tracks
                </span>
            </div>

            <!-- Search Form for Songs -->
            <form action="{{ route('admin.dashboard') }}" method="GET" class="d-flex align-items-center gap-2">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                @if(request('withdrawal_status'))
                    <input type="hidden" name="withdrawal_status" value="{{ request('withdrawal_status') }}">
                @endif
                <div class="input-group input-group-sm" style="min-width: 260px; max-width: 340px;">
                    <span class="input-group-text bg-dark border-secondary border-opacity-25 text-secondary">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </span>
                    <input type="text" name="song_search" class="form-control form-control-sm exact-input" placeholder="Search track, artist, customer..." value="{{ $songSearch ?? '' }}">
                    @if(!empty($songSearch))
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary border-secondary border-opacity-25 text-light" title="Clear Search">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                    <button class="btn btn-sm btn-exact-teal" type="submit">Filter</button>
                </div>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table-custom-dark">
                <thead>
                    <tr>
                        <th style="width: 72px;">Cover</th>
                        <th>Song Information</th>
                        <th>Customer</th>
                        <th style="min-width: 240px;">MP3 Stream</th>
                        <th class="text-end" style="min-width: 170px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allSongs as $song)
                        <tr>
                            <!-- 3000x3000px Cover Artwork Thumbnail -->
                            <td>
                                <div class="position-relative" style="width: 58px; height: 58px; border-radius: 10px; overflow: hidden; border: 1.5px solid rgba(0, 210, 170, 0.35); background: #070b14; box-shadow: 0 4px 10px rgba(0,0,0,0.5);">
                                    <img src="{{ $song->cover_image_url }}" alt="{{ $song->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    <button type="button" class="btn btn-sm position-absolute top-0 end-0 p-0.5 m-0.5 rounded-circle" style="background: rgba(0,0,0,0.75); color: #00d2aa; border: none; font-size: 0.65rem; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center;" title="Zoom 3000 × 3000 px Cover Art" data-bs-toggle="modal" data-bs-target="#adminCoverZoomModal{{ $song->id }}">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i>
                                    </button>
                                </div>
                            </td>

                            <!-- Complete Song Details: Title, Singer, Composer, Producer, Copyright -->
                            <td>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <div class="fw-bold text-light" style="font-size: 0.94rem;">{{ $song->title }}</div>
                                    <span class="badge rounded-pill small px-2 py-0.5" style="background: rgba(0, 210, 170, 0.15); color: #00d2aa; font-size: 0.68rem; border: 1px solid rgba(0, 210, 170, 0.3);">
                                        3000 &times; 3000 px
                                    </span>
                                </div>
                                <div class="small mb-1" style="color: #00d2aa; font-size: 0.82rem; font-weight: 600;">
                                    <i class="fa-solid fa-microphone me-1"></i> Singer: {{ $song->singer }}
                                </div>
                                <div class="small text-secondary" style="font-size: 0.78rem;">
                                    <span>Lyrics/Composer: <strong class="text-light">{{ $song->composer }}</strong></span> &bull; 
                                    <span>Producer: <strong class="text-light">{{ $song->producer }}</strong></span>
                                </div>
                                <div class="mt-1 d-inline-flex align-items-center gap-1 px-2 py-0.5 rounded-pill small" style="background: rgba(0, 210, 170, 0.08); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.25); font-size: 0.72rem;">
                                    <i class="fa-regular fa-copyright"></i> {{ $song->copyright ?: '℗ 2026 Rajdoot Nivedan' }}
                                </div>
                            </td>

                            <!-- Associated Customer Profile -->
                            <td>
                                @if($song->user)
                                    <div class="d-flex align-items-center gap-2">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #00362c; border: 1.5px solid var(--teal); color: var(--teal); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.78rem; flex-shrink: 0;">
                                            {{ strtoupper(substr($song->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('admin.customers.show', $song->user->id) }}" class="fw-bold text-light text-decoration-none d-block" style="font-size: 0.88rem;" title="View Customer Profile">
                                                {{ $song->user->name }}
                                            </a>
                                            <div class="small" style="color: #94a3b8; font-size: 0.76rem;">
                                                {{ $song->user->username ? '@'.$song->user->username : $song->user->email }}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-secondary small">&mdash; Unknown Customer &mdash;</span>
                                @endif
                            </td>

                            <!-- MP3 Stream & Player -->
                            <td>
                                <div class="p-2 rounded-3" style="background: rgba(0, 0, 0, 0.35); border: 1px solid rgba(255, 255, 255, 0.06);">
                                    <div class="d-flex align-items-center justify-content-between small mb-1" style="font-size: 0.74rem;">
                                        <span class="text-teal fw-semibold"><i class="fa-solid fa-play me-1"></i> MP3 Audio</span>
                                        <span class="text-secondary">{{ $song->created_at->format('M d, Y') }}</span>
                                    </div>
                                    <audio controls class="w-100" style="height: 32px; border-radius: 6px;" preload="none" src="{{ $song->audio_file_url }}"></audio>
                                </div>
                            </td>

                            <!-- Actions: Download MP3, Inspect Cover, Edit, Delete -->
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <!-- Download MP3 Button -->
                                    <a href="{{ route('admin.songs.download', $song->id) }}" class="btn btn-sm btn-exact-teal py-1.5 px-3 small fw-bold d-inline-flex align-items-center gap-1.5 text-nowrap" title="Download original MP3 audio file">
                                        <i class="fa-solid fa-download"></i> <span>Download MP3</span>
                                    </a>

                                    <!-- Cover Zoom Preview Button -->
                                    <button type="button" class="admin-action-link" data-bs-toggle="modal" data-bs-target="#adminCoverZoomModal{{ $song->id }}" title="Inspect 3000 × 3000 px Cover Artwork">
                                        <i class="fa-regular fa-image"></i>
                                    </button>

                                    <!-- Edit Song Modal Trigger -->
                                    <button type="button" class="admin-action-link" data-bs-toggle="modal" data-bs-target="#adminMainEditSongModal{{ $song->id }}" title="Edit Song Details">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>

                                    <!-- Delete Song Form -->
                                    <form action="{{ route('admin.songs.destroy', $song->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Permanently delete song &quot;{{ $song->title }}&quot; and its audio/cover files?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-action-link delete-link" title="Delete Song">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal: Zoom 3000x3000px Cover Artwork -->
                        <div class="modal fade" id="adminCoverZoomModal{{ $song->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(0,210,170,0.3); border-radius: 18px; box-shadow: 0 25px 50px rgba(0,0,0,0.85);">
                                    <div class="modal-header border-secondary border-opacity-25">
                                        <h5 class="modal-title fw-bold text-light fs-6">
                                            <i class="fa-regular fa-image text-teal me-2"></i> 3000 &times; 3000 px Artwork: {{ $song->title }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-4 text-center">
                                        <div class="p-2 rounded-3 bg-black border border-secondary border-opacity-25 d-inline-block mb-3">
                                            <img src="{{ $song->cover_image_url }}" alt="{{ $song->title }}" class="img-fluid rounded" style="max-height: 480px; object-fit: contain;">
                                        </div>
                                        <div class="d-flex align-items-center justify-content-center gap-3 flex-wrap">
                                            <span class="badge px-3 py-1.5 rounded-pill" style="background: rgba(0, 210, 170, 0.15); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.35);">
                                                Full 3000 &times; 3000 px High-Res Artwork
                                            </span>
                                            <a href="{{ $song->cover_image_url }}" download="{{ Str::slug($song->title) }}-cover.jpg" target="_blank" class="btn btn-sm btn-exact-teal py-1.5 px-3 rounded-pill fw-bold">
                                                <i class="fa-solid fa-download me-1"></i> Download Cover
                                            </a>
                                            <a href="{{ $song->cover_image_url }}" target="_blank" class="btn btn-sm btn-dark-outline py-1.5 px-3 rounded-pill">
                                                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Open Full Image
                                            </a>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-secondary border-opacity-25">
                                        <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal: Edit Song from Main Dashboard -->
                        <div class="modal fade" id="adminMainEditSongModal{{ $song->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.12); border-radius: 18px; box-shadow: 0 25px 50px rgba(0,0,0,0.85);">
                                    <div class="modal-header border-secondary border-opacity-25">
                                        <h5 class="modal-title fw-bold text-light fs-6">
                                            <i class="fa-solid fa-pen-to-square text-teal me-2"></i> Edit Song: {{ $song->title }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.songs.update', $song->id) }}" method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4 text-start">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-secondary">Song Title</label>
                                                    <input type="text" name="title" class="form-control exact-input" value="{{ $song->title }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-secondary">Singer</label>
                                                    <input type="text" name="singer" class="form-control exact-input" value="{{ $song->singer }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-secondary">Lyrics / Composer</label>
                                                    <input type="text" name="composer" class="form-control exact-input" value="{{ $song->composer }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-secondary">Producer</label>
                                                    <input type="text" name="producer" class="form-control exact-input" value="{{ $song->producer }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-secondary">Replace Cover (Optional)</label>
                                                    <input type="file" name="cover_image" class="form-control exact-input" accept="image/jpeg,image/png,image/jpg,image/webp">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small fw-semibold text-secondary">Replace MP3 (Optional)</label>
                                                    <input type="file" name="audio_file" class="form-control exact-input" accept=".mp3,audio/mpeg,audio/mp3">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label small fw-semibold text-secondary">Copyright &mdash; P-Line</label>
                                                    <input type="text" name="copyright" class="form-control exact-input" value="{{ $song->copyright ?: '℗ 2026 Rajdoot Nivedan' }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary border-opacity-25">
                                            <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-exact-teal py-1.5 px-3">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color: #64748b; font-size: 0.86rem;">
                                No customer songs uploaded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($allSongs->hasPages())
            <div class="mt-3 d-flex justify-content-end">
                {{ $allSongs->appends(request()->except('songs_page'))->links('pagination::bootstrap-5') }}
            </div>
        @endif

    </div>

    <!-- =========================================================================
         CARD 5: COPYRIGHT CLAIM REMOVE LINKS (Exact match to Reference Image)
         ========================================================================= -->
    <div class="exact-card mb-4">
        
        <!-- Header with Count Badge (Exact match to Reference Image) -->
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-link text-teal" style="font-size: 0.95rem;"></i>
            <h2 class="h6 fw-bold text-light mb-0" style="font-size: 0.92rem;">Copyright claim remove links</h2>
            <span class="badge rounded-pill fw-bold px-2.5 py-0.5" style="background: rgba(0, 210, 170, 0.2); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.4); font-size: 0.72rem;">
                {{ $allClaimLinks->count() }}/10
            </span>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table-custom-dark">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Customer</th>
                        <th>Title</th>
                        <th>Link</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allClaimLinks as $cLink)
                        <tr>
                            <!-- # Number Circle Badge -->
                            <td>
                                <div class="d-inline-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; border-radius: 50%; background: rgba(0, 210, 170, 0.12); color: #00d2aa; font-size: 0.78rem;">
                                    {{ $cLink->slot_number }}
                                </div>
                            </td>

                            <!-- Customer -->
                            <td>
                                <span class="fw-semibold text-light" style="font-size: 0.88rem;">
                                    {{ $cLink->user->username ?: $cLink->user->name }}
                                </span>
                            </td>

                            <!-- Title -->
                            <td>
                                <span class="text-light" style="font-size: 0.88rem;">
                                    {{ $cLink->display_title }}
                                </span>
                            </td>

                            <!-- Link -->
                            <td>
                                <a href="{{ $cLink->url }}" target="_blank" rel="noopener noreferrer" class="small text-decoration-none text-truncate d-inline-block" style="color: #22d3ee !important; max-width: 380px;" title="{{ $cLink->url }}">
                                    {{ $cLink->url }}
                                </a>
                            </td>

                            <!-- Actions: [✏️ Edit] [🗑️ Delete] -->
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <!-- Edit Link Button -->
                                    <button type="button" class="admin-action-link" data-bs-toggle="modal" data-bs-target="#adminEditLinkModal{{ $cLink->id }}">
                                        <i class="fa-regular fa-pen-to-square"></i> Edit
                                    </button>

                                    <!-- Delete Link Button -->
                                    <form action="{{ route('admin.copyright_links.destroy', $cLink->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete claim link from Slot #{{ $cLink->slot_number }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="admin-action-link delete-link">
                                            <i class="fa-regular fa-trash-can"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal: Edit Claim Link -->
                        <div class="modal fade" id="adminEditLinkModal{{ $cLink->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
                                    <div class="modal-header border-secondary border-opacity-25">
                                        <h5 class="modal-title fw-bold text-light fs-6">Edit Claim Link (Slot #{{ $cLink->slot_number }})</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('admin.copyright_links.update', $cLink->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4 text-start">
                                            <div class="mb-3">
                                                <label class="form-label small" style="color: #94a3b8;">Slot Number (1 - 10)</label>
                                                <input type="number" name="slot_number" min="1" max="10" class="form-control exact-input" value="{{ $cLink->slot_number }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small" style="color: #94a3b8;">Title (e.g. song name)</label>
                                                <input type="text" name="title" class="form-control exact-input" value="{{ $cLink->title }}">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label small" style="color: #94a3b8;">Target Link (URL)</label>
                                                <input type="url" name="url" class="form-control exact-input" value="{{ $cLink->url }}" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-secondary border-opacity-25">
                                            <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-exact-teal py-1.5 px-3">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color: #64748b; font-size: 0.86rem;">
                                No copyright claim links uploaded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

<!-- =========================================================================
     GLOBAL MODALS
     ========================================================================= -->

<!-- MODAL: ADD / ASSIGN COPYRIGHT CLAIM LINK -->
<div class="modal fade" id="adminAddClaimLinkModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light fs-6"><i class="fa-solid fa-link text-teal me-2"></i> Add / Assign Claim Link</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.copyright_links.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small" style="color: #94a3b8;">Assign to Customer (Optional)</label>
                        <select name="user_id" class="form-select exact-input">
                            <option value="">-- Main Admin / Platform Link --</option>
                            @foreach($users as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ $cust->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small" style="color: #94a3b8;">Slot Number (1 - 10)</label>
                        <select name="slot_number" class="form-select exact-input" required>
                            @for($s = 1; $s <= 10; $s++)
                                <option value="{{ $s }}">Slot #{{ $s }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small" style="color: #94a3b8;">Title (e.g. song name)</label>
                        <input type="text" name="title" class="form-control exact-input" placeholder="Title (optional)">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small" style="color: #94a3b8;">Target Link (URL)</label>
                        <input type="url" name="url" class="form-control exact-input" placeholder="https://..." required>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-exact-teal py-1.5 px-3">Save Link</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: ADD NEW CUSTOMER -->
<div class="modal fade" id="adminCreateCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light fs-6">
                    <i class="fa-solid fa-user-plus text-teal me-2"></i> Create New Customer
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.customers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Full Name</label>
                        <input type="text" name="name" class="form-control exact-input" required placeholder="e.g. John Doe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Username (Optional)</label>
                        <input type="text" name="username" class="form-control exact-input" placeholder="e.g. johndoe">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Email Address</label>
                        <input type="email" name="email" class="form-control exact-input" required placeholder="john@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Phone Number (Optional)</label>
                        <input type="text" name="phone" class="form-control exact-input" placeholder="+91 9876543210">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold" style="color: #94a3b8;">Password</label>
                        <input type="password" name="password" class="form-control exact-input" required placeholder="Minimum 6 characters">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Initial Balance (₹)</label>
                            <input type="number" step="0.01" min="0" name="earning_balance" class="form-control exact-input" value="0.00">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Total Earnings (₹)</label>
                            <input type="number" step="0.01" min="0" name="total_earnings" class="form-control exact-input" value="0.00">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-exact-teal py-1.5 px-3">Create Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
