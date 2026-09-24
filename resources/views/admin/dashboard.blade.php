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
        <div class="col-sm-6 col-lg-3">
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

        <!-- 2. PENDING KYC -->
        <div class="col-sm-6 col-lg-3">
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

        <!-- 3. PENDING WITHDRAWALS -->
        <div class="col-sm-6 col-lg-3">
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

        <!-- 4. TOTAL PAID OUT -->
        <div class="col-sm-6 col-lg-3">
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
                        <!-- View Signature Modal Trigger -->
                        <button type="button" class="btn btn-sm btn-dark-outline py-1.5 px-3 rounded-pill small" data-bs-toggle="modal" data-bs-target="#viewDocModal{{ $v->id }}">
                            <i class="fa-regular fa-eye me-1 text-teal"></i> Signature
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
                                        @if($v->pan_card_photo && file_exists(public_path('storage/' . $v->pan_card_photo)))
                                            <img src="{{ asset('storage/' . $v->pan_card_photo) }}" alt="PAN Card" class="img-fluid rounded" style="max-height: 260px; object-fit: contain;">
                                        @else
                                            <div class="py-5 text-secondary small">
                                                <i class="fa-regular fa-id-card fs-1 d-block mb-2 text-teal opacity-50"></i>
                                                {{ $v->pan_card_photo ?: 'No PAN card uploaded' }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fw-bold small text-info mb-2">Signature Photo</div>
                                    <div class="p-2 rounded-3 border border-secondary border-opacity-25" style="background: #070b14;">
                                        @if($v->signature_photo && file_exists(public_path('storage/' . $v->signature_photo)))
                                            <img src="{{ asset('storage/' . $v->signature_photo) }}" alt="Signature" class="img-fluid rounded" style="max-height: 260px; object-fit: contain;">
                                        @else
                                            <div class="py-5 text-secondary small">
                                                <i class="fa-solid fa-signature fs-1 d-block mb-2 text-info opacity-50"></i>
                                                {{ $v->signature_photo ?: 'No signature uploaded' }}
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
                                        <div class="mt-1" style="font-size: 0.75rem;">
                                            @if($user->autocart_generator_enabled)
                                                <span class="fw-bold text-success" style="color: #10b981 !important;">
                                                    <i class="fa-solid fa-circle" style="font-size: 6px; vertical-align: middle; margin-right: 3px;"></i> Autocart Generator is ON
                                                </span>
                                            @else
                                                <span class="fw-bold text-danger" style="color: #ef4444 !important;">
                                                    <i class="fa-solid fa-circle" style="font-size: 6px; vertical-align: middle; margin-right: 3px;"></i> Autocart Generator is OFF
                                                </span>
                                            @endif
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

                            <!-- KYC Badge -->
                            <td>
                                @if($user->isVerified())
                                    <span class="badge-approved">verified</span>
                                @elseif($user->verification && $user->verification->status === 'pending')
                                    <span class="badge-pending">pending</span>
                                @elseif($user->verification && $user->verification->status === 'rejected')
                                    <span class="badge-rejected">rejected</span>
                                @else
                                    <span class="badge-unverified">unverified</span>
                                @endif
                            </td>

                            <!-- Withdrawn -->
                            <td class="small" style="color: #94a3b8; font-size: 0.88rem;">
                                ₹{{ number_format($user->withdrawn_amount, 0) }}
                            </td>

                            <!-- Actions: [✏️ Earnings] [🪪 Profile Info] [👁️ View] -->
                            <td class="text-end">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <!-- Earnings Modal Trigger -->
                                    <button type="button" class="admin-action-link" data-bs-toggle="modal" data-bs-target="#manageEarningsModal{{ $user->id }}" title="Manage Earnings for {{ $user->name }}">
                                        <i class="fa-solid fa-pen-to-square"></i> Earnings
                                    </button>

                                    <!-- View Profile Info Modal Trigger -->
                                    <button type="button" class="admin-action-link" data-bs-toggle="modal" data-bs-target="#viewProfileInfoModal{{ $user->id }}" title="View Profile Info for {{ $user->name }}">
                                        <i class="fa-solid fa-id-card"></i> View Profile Info
                                        @if($user->isProfileComplete())
                                            <span class="profile-status-dot dot-green ms-1" title="Profile Complete (Green)"></span>
                                        @else
                                            <span class="profile-status-dot dot-red ms-1" title="Profile Incomplete (Red)"></span>
                                        @endif
                                    </button>

                                    <!-- View Individual Customer Profile Link -->
                                    <a href="{{ route('admin.customers.show', $user->id) }}" class="admin-action-link" title="Open full customer profile & manage songs">
                                        <i class="fa-regular fa-eye"></i> View
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
                                        </div>

                                        @if($user->profileInfo)
                                            <div class="mt-3 text-end small text-secondary" style="font-size: 0.75rem;">
                                                Last updated: {{ $user->profileInfo->updated_at->format('M d, Y h:i A') }}
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
         CARD 4: COPYRIGHT CLAIM REMOVE LINKS (Exact match to Reference Image)
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
