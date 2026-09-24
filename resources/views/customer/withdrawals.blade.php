@extends('layouts.app')

@section('title', 'Withdrawal History - Rajdoot Nivedan Media')

@section('content')
<div class="dashboard-page-wrapper">

    <!-- Breadcrumb & Page Header -->
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item">
                        <a href="{{ route('customer.dashboard') }}" class="text-decoration-none text-teal">
                            <i class="fa-solid fa-gauge me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-light" aria-current="page">Withdrawal History</li>
                </ol>
            </nav>
            <h1 class="h3 fw-extrabold text-light mb-1 d-flex align-items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-teal" style="font-size: 1.3rem;"></i>
                Withdrawal History &amp; Payouts
            </h1>
            <p class="mb-0" style="color: #94a3b8; font-size: 0.9rem;">
                Track your requested payouts, approval status, and real-time transaction debit/credit records.
            </p>
        </div>

        <div class="d-flex flex-wrap align-items-center gap-2">
            @if($user->earningTransactions->count() > 0)
                <button type="button" class="btn btn-sm btn-dark-outline py-2 px-3 rounded-3 small" data-bs-toggle="modal" data-bs-target="#statementModal">
                    <i class="fa-solid fa-receipt me-1 text-teal"></i> View Statement
                </button>
            @endif

            @if($user->isVerified())
                <button type="button" class="btn btn-sm btn-exact-teal py-2 px-3 rounded-3 small" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                    <i class="fa-solid fa-hand-holding-dollar me-1"></i> Request Withdrawal
                </button>
            @else
                <button type="button" class="btn btn-sm py-2 px-3 rounded-3 small opacity-75" disabled style="background: #151c2a; border: 1px solid rgba(255,255,255,0.08); color: #94a3b8;">
                    <i class="fa-solid fa-lock me-1"></i> Verify to Withdraw
                </button>
            @endif

            <a href="{{ route('customer.dashboard') }}" class="btn btn-sm btn-dark-outline py-2 px-3 rounded-3 small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
            </a>
        </div>
    </div>

    <!-- Quick Stat Cards Row -->
    <div class="row g-3 mb-4">
        <!-- Available Balance -->
        <div class="col-sm-6 col-lg-3">
            <div class="exact-stat-card">
                <div class="text-uppercase small fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #94a3b8;">
                    AVAILABLE BALANCE
                </div>
                <div class="fs-4 fw-extrabold" style="color: #00d2aa;">
                    ₹{{ number_format($user->earning_balance, 2) }}
                </div>
                <div class="small mt-1 text-muted" style="font-size: 0.78rem;">
                    Ready for withdrawal
                </div>
            </div>
        </div>

        <!-- Total Withdrawn -->
        <div class="col-sm-6 col-lg-3">
            <div class="exact-stat-card">
                <div class="text-uppercase small fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #94a3b8;">
                    TOTAL WITHDRAWN
                </div>
                <div class="fs-4 fw-extrabold" style="color: #22d3ee;">
                    ₹{{ number_format($user->withdrawn_amount, 2) }}
                </div>
                <div class="small mt-1 text-muted" style="font-size: 0.78rem;">
                    Processed lifetime
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <div class="col-sm-6 col-lg-3">
            <div class="exact-stat-card">
                <div class="text-uppercase small fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #94a3b8;">
                    PENDING REQUESTS
                </div>
                <div class="fs-4 fw-extrabold" style="color: #c084fc;">
                    {{ $user->withdrawals->where('status', 'pending')->count() }}
                </div>
                <div class="small mt-1 text-muted" style="font-size: 0.78rem;">
                    Awaiting admin review
                </div>
            </div>
        </div>

        <!-- Verification Status -->
        <div class="col-sm-6 col-lg-3">
            <div class="exact-stat-card">
                <div class="text-uppercase small fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #94a3b8;">
                    PROFILE KYC STATUS
                </div>
                <div class="fs-5 fw-extrabold mt-1">
                    @if($user->isVerified())
                        <span style="color: #00d2aa;"><i class="fa-solid fa-circle-check me-1"></i> Verified</span>
                    @elseif($user->verification && $user->verification->status === 'pending')
                        <span class="text-light"><i class="fa-regular fa-clock me-1"></i> Under Review</span>
                    @elseif($user->verification && $user->verification->status === 'rejected')
                        <span class="text-danger"><i class="fa-solid fa-circle-xmark me-1"></i> Rejected</span>
                    @else
                        <span style="color: #facc15;"><i class="fa-solid fa-triangle-exclamation me-1"></i> Unverified</span>
                    @endif
                </div>
                <div class="small mt-1" style="font-size: 0.78rem;">
                    @if($user->isVerified())
                        <span class="text-teal">Payouts enabled</span>
                    @else
                        <a href="{{ route('customer.dashboard') }}" class="text-warning text-decoration-none">Complete KYC &rarr;</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card: Withdrawal History Table -->
    <div class="exact-card mb-4">
        
        <!-- Header -->
        <div class="d-flex align-items-center justify-content-between mb-3 pb-1">
            <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-teal" style="font-size: 0.95rem;"></i>
                <h2 class="h6 fw-bold text-light mb-0" style="font-size: 0.92rem;">
                    Withdrawal Requests ({{ $user->withdrawals->count() }})
                </h2>
            </div>
            @if($user->earningTransactions->count() > 0)
                <button type="button" class="btn btn-sm btn-dark-outline py-1 px-3 rounded-pill small" data-bs-toggle="modal" data-bs-target="#statementModal">
                    <i class="fa-solid fa-receipt me-1 text-teal"></i> View Statement
                </button>
            @endif
        </div>

        @if($user->withdrawals->count() === 0)
            <!-- Empty State -->
            <div class="text-center py-5">
                <div class="mb-3">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(0, 210, 170, 0.08); border: 1px dashed rgba(0, 210, 170, 0.3); display: inline-flex; align-items: center; justify-content: center; color: #00d2aa; font-size: 1.4rem;">
                        <i class="fa-solid fa-wallet"></i>
                    </div>
                </div>
                <h4 class="h6 fw-bold text-light mb-1">No withdrawals yet</h4>
                <p class="small mb-0" style="color: #64748b; font-size: 0.86rem; max-width: 420px; margin: 0 auto;">
                    Your payout requests will appear here once submitted. You can request a withdrawal once your KYC profile is verified.
                </p>
            </div>
        @else
            <!-- Populated Table -->
            <div class="table-responsive">
                <table class="table-custom-dark">
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>Amount</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th class="text-end">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($user->withdrawals as $w)
                            <tr>
                                <td>
                                    <div class="fw-bold text-light">#WD-{{ $w->id }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold fs-6" style="color: #00d2aa;">₹{{ number_format($w->amount, 2) }}</span>
                                </td>
                                <td class="small" style="color: #94a3b8;">
                                    {{ $w->created_at->format('n/j/Y') }}
                                </td>
                                <td>
                                    @if($w->status === 'approved')
                                        <span class="badge-approved">approved</span>
                                    @elseif($w->status === 'rejected')
                                        <span class="badge-rejected">rejected</span>
                                    @else
                                        <span class="badge-pending">pending</span>
                                    @endif
                                </td>
                                <td class="text-end small" style="color: #94a3b8;">
                                    {{ $w->admin_note ?? 'Standard processing' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>

</div>

<!-- =========================================================================
     MODALS
     ========================================================================= -->

<!-- MODAL 1: WITHDRAWAL REQUEST MODAL -->
@if($user->isVerified())
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light fs-6">
                    <i class="fa-solid fa-hand-holding-dollar text-teal me-2"></i> Request Withdrawal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.withdrawal.request') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="p-3 rounded-3 mb-3" style="background: #111726; border: 1px solid rgba(0, 210, 170, 0.25);">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="small" style="color: #94a3b8;">Available Balance:</span>
                            <span class="fw-bold" style="color: #00d2aa;">₹{{ number_format($user->earning_balance, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between small" style="color: #94a3b8;">
                            <span>Payout Account:</span>
                            <span class="text-light fw-semibold">{{ $user->verification->bank_account }} ({{ $user->verification->ifsc_code }})</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label small fw-semibold" style="color: #94a3b8;">Amount to Withdraw (₹) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="100" max="{{ $user->earning_balance }}" name="amount" id="amount" class="form-control exact-input" style="font-size: 1.15rem; font-weight: 700;" placeholder="0.00" required>
                        <div class="small mt-1" style="color: #64748b;">Minimum withdrawal: ₹100.00 | Maximum: ₹{{ number_format($user->earning_balance, 2) }}</div>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-exact-teal py-1.5 px-3">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<!-- MODAL 2: EARNINGS STATEMENT MODAL -->
<div class="modal fade" id="statementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light fs-6">
                    <i class="fa-solid fa-receipt text-teal me-2"></i> Royalty &amp; Earnings Statement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 420px;">
                    <table class="table-custom-dark">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Amount</th>
                                <th>Balance After</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($user->earningTransactions as $tx)
                                <tr>
                                    <td class="small" style="color: #94a3b8;">{{ $tx->created_at->format('d M Y, h:i A') }}</td>
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
                                    <td class="small" style="color: #94a3b8;">{{ $tx->note }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-secondary small">No transactions logged yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-secondary border-opacity-25">
                <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection
