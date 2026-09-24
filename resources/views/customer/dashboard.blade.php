@extends('layouts.app')

@section('title', 'Customer Dashboard - Rajdoot Nivedan Media')

@section('content')
<div class="dashboard-page-wrapper">

    <!-- =========================================================================
         1. WELCOME BANNER CARD (Exact match to Reference Image)
         ========================================================================= -->
    <div class="exact-card position-relative overflow-hidden mb-3">
        <div class="row align-items-center g-4">
            
            <div class="col-lg-7">
                <div class="text-uppercase small fw-bold mb-2" style="color: #00d2aa; letter-spacing: 1.2px; font-size: 0.72rem;">
                    RAJDOOT NIVEDAN MEDIA
                </div>
                <h1 class="h2 fw-extrabold text-light mb-2" style="letter-spacing: -0.4px;">
                    Welcome, <span style="color: #00d2aa;">{{ $user->username ?: $user->name }}</span>
                </h1>
                <p class="mb-0" style="color: #94a3b8; font-size: 0.92rem;">
                    Your royalties are growing in real time. Keep distributing &mdash; keep earning.
                </p>
            </div>

            <div class="col-lg-5">
                <div style="height: 110px;" class="position-relative">
                    <canvas id="welcomeWaveChart" class="w-100 h-100"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- =========================================================================
         2. EARN SECTION CARD (Exact match to Reference Image)
         ========================================================================= -->
    <div class="exact-card mb-3">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-4">
            
            <!-- Left: EARN Title, Live Badge & Large Amount -->
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="fs-5 fw-extrabold" style="color: #00d2aa; letter-spacing: 0.5px;">EARN</span>
                    <span class="badge px-2 py-0.5 rounded-pill fw-bold" style="background: rgba(0, 210, 170, 0.12); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.3); font-size: 0.68rem;">
                        <span class="pulsing-dot me-1"></span> LIVE
                    </span>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span style="color: #00d2aa; font-size: 1.8rem; line-height: 1;">&bull;</span>
                    <span class="display-5 fw-extrabold text-light" style="letter-spacing: -0.5px;">
                        ₹{{ number_format($user->earning_balance, 2) }}
                    </span>
                </div>

                <div class="small mt-1 d-flex align-items-center gap-1.5" style="color: #94a3b8; font-size: 0.82rem;">
                    <i class="fa-solid fa-arrow-trend-up text-teal" style="font-size: 0.75rem;"></i>
                    <span>Available balance &middot; INR</span>
                </div>
            </div>

            <!-- Right: Withdraw Action Guarded by Verification -->
            <div class="text-md-end">
                @if($user->isVerified())
                    <button type="button" class="btn btn-exact-teal py-2.5 px-4" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                        <i class="fa-solid fa-hand-holding-dollar me-1.5"></i> WITHDRAW
                    </button>
                    <div class="text-teal small mt-1.5" style="font-size: 0.78rem;">
                        <i class="fa-solid fa-circle-check me-1"></i> Profile verified &amp; ready
                    </div>
                @else
                    <button type="button" class="btn py-2.5 px-4 rounded-3 fw-bold small opacity-75" disabled style="background: #151c2a; border: 1px solid rgba(255,255,255,0.08); color: #94a3b8;">
                        <i class="fa-solid fa-lock me-1.5" style="font-size: 0.8rem;"></i> WITHDRAW
                    </button>
                    <div class="mt-1.5">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#verifyModal" class="text-decoration-none small fw-semibold" style="color: #facc15; font-size: 0.8rem;">
                            <i class="fa-solid fa-lock me-1"></i> Verify profile to withdraw
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- =========================================================================
         3. PROFILE VERIFICATION BANNER (Exact match to Reference Image)
         ========================================================================= -->
    <div class="exact-card py-3 px-4 d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3 mb-3">
        
        <div class="d-flex align-items-center gap-3">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: rgba(34, 211, 238, 0.08); border: 1px solid rgba(34, 211, 238, 0.25); display: flex; align-items: center; justify-content: center; color: #22d3ee; font-size: 1.1rem;">
                <i class="fa-regular fa-clock"></i>
            </div>
            <div>
                <div class="small" style="color: #94a3b8; font-size: 0.78rem;">Profile verification</div>
                <div class="fw-bold" style="font-size: 0.95rem;">
                    @if($user->isVerified())
                        <span style="color: #00d2aa;">Verified</span>
                    @elseif($user->verification && $user->verification->status === 'pending')
                        <span class="text-light">Under Review</span>
                    @elseif($user->verification && $user->verification->status === 'rejected')
                        <span class="text-danger">Rejected</span>
                    @else
                        <span style="color: #facc15;">Unverified</span>
                    @endif
                </div>
            </div>
        </div>

        <div>
            @if($user->isVerified())
                <span class="badge px-3 py-2 rounded-pill fw-bold" style="background: rgba(0, 210, 170, 0.12); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.3);">
                    <i class="fa-solid fa-circle-check me-1"></i> Verified
                </span>
            @else
                <button type="button" class="btn btn-exact-teal py-2 px-3.5" data-bs-toggle="modal" data-bs-target="#verifyModal">
                    <i class="fa-solid fa-shield-halved me-1.5"></i> Update details
                </button>
            @endif
        </div>

    </div>

    <!-- =========================================================================
         4. FOUR KEY STAT CARDS ROW (Exact match to Reference Image)
         ========================================================================= -->
    <div class="row g-3 mb-3">
        
        <!-- Total Earnings -->
        <div class="col-sm-6 col-lg-3">
            <div class="exact-stat-card">
                <div class="text-uppercase small fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #94a3b8;">
                    TOTAL EARNINGS
                </div>
                <div class="fs-4 fw-extrabold" style="color: #facc15;">
                    ₹{{ number_format($user->total_earnings, 2) }}
                </div>
            </div>
        </div>

        <!-- Available Balance -->
        <div class="col-sm-6 col-lg-3">
            <div class="exact-stat-card">
                <div class="text-uppercase small fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #94a3b8;">
                    AVAILABLE BALANCE
                </div>
                <div class="fs-4 fw-extrabold" style="color: #00d2aa;">
                    ₹{{ number_format($user->earning_balance, 2) }}
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
            </div>
        </div>

        <!-- Open Requests -->
        <div class="col-sm-6 col-lg-3">
            <div class="exact-stat-card">
                <div class="text-uppercase small fw-bold mb-2" style="font-size: 0.68rem; letter-spacing: 0.5px; color: #94a3b8;">
                    OPEN REQUESTS
                </div>
                <div class="fs-4 fw-extrabold" style="color: #c084fc;">
                    {{ $user->withdrawals->where('status', 'pending')->count() }}
                </div>
            </div>
        </div>

    </div>

    <!-- =========================================================================
         5. QUICK ACCESS SHORTCUTS TO DEDICATED SECTIONS
         ========================================================================= -->
    <div class="row g-3 mb-4">
        
        <!-- Option 1: Copyright Claim Remove Links -->
        <div class="col-md-4">
            <a href="{{ route('customer.copyright_links.index') }}" class="exact-card d-block text-decoration-none p-3.5 h-100 transition-all hover-scale" style="background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.08);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(0, 210, 170, 0.12); color: #00d2aa; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; border: 1px solid rgba(0, 210, 170, 0.25);">
                        <i class="fa-solid fa-link"></i>
                    </div>
                    <span class="badge px-2.5 py-1 rounded-pill small" style="background: rgba(0, 210, 170, 0.1); color: #00d2aa; border: 1px solid rgba(0, 210, 170, 0.25); font-size: 0.72rem;">
                        {{ $user->copyrightClaimLinks->count() }} Active
                    </span>
                </div>
                <div class="fw-bold text-light mb-1 d-flex align-items-center justify-content-between" style="font-size: 0.96rem;">
                    <span>Copyright Claim Links</span>
                    <i class="fa-solid fa-arrow-right text-teal small"></i>
                </div>
                <div class="small" style="color: #94a3b8; font-size: 0.82rem; line-height: 1.4;">
                    Upload and manage copyright claim links published on Home Page slots.
                </div>
            </a>
        </div>

        <!-- Option 2: Upload Song -->
        <div class="col-md-4">
            <a href="{{ route('customer.songs.index') }}" class="exact-card d-block text-decoration-none p-3.5 h-100 transition-all hover-scale" style="background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.08);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(34, 211, 238, 0.12); color: #22d3ee; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; border: 1px solid rgba(34, 211, 238, 0.25);">
                        <i class="fa-solid fa-music"></i>
                    </div>
                    <span class="badge px-2.5 py-1 rounded-pill small" style="background: rgba(34, 211, 238, 0.1); color: #22d3ee; border: 1px solid rgba(34, 211, 238, 0.25); font-size: 0.72rem;">
                        {{ $user->songs->count() }} Track{{ $user->songs->count() === 1 ? '' : 's' }}
                    </span>
                </div>
                <div class="fw-bold text-light mb-1 d-flex align-items-center justify-content-between" style="font-size: 0.96rem;">
                    <span>Upload Song</span>
                    <i class="fa-solid fa-arrow-right small" style="color: #22d3ee;"></i>
                </div>
                <div class="small" style="color: #94a3b8; font-size: 0.82rem; line-height: 1.4;">
                    Upload 3000 &times; 3000 px artwork and high-res MP3 audio tracks.
                </div>
            </a>
        </div>

        <!-- Option 3: Withdrawal History -->
        <div class="col-md-4">
            <a href="{{ route('customer.withdrawals.index') }}" class="exact-card d-block text-decoration-none p-3.5 h-100 transition-all hover-scale" style="background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.08);">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div style="width: 42px; height: 42px; border-radius: 10px; background: rgba(192, 132, 252, 0.12); color: #c084fc; display: flex; align-items: center; justify-content: center; font-size: 1.15rem; border: 1px solid rgba(192, 132, 252, 0.25);">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>
                    <span class="badge px-2.5 py-1 rounded-pill small" style="background: rgba(192, 132, 252, 0.1); color: #c084fc; border: 1px solid rgba(192, 132, 252, 0.25); font-size: 0.72rem;">
                        {{ $user->withdrawals->count() }} Payout{{ $user->withdrawals->count() === 1 ? '' : 's' }}
                    </span>
                </div>
                <div class="fw-bold text-light mb-1 d-flex align-items-center justify-content-between" style="font-size: 0.96rem;">
                    <span>Withdrawal History</span>
                    <i class="fa-solid fa-arrow-right small" style="color: #c084fc;"></i>
                </div>
                <div class="small" style="color: #94a3b8; font-size: 0.82rem; line-height: 1.4;">
                    View payment records, payout approval statuses, and royalty statements.
                </div>
            </a>
        </div>

    </div>

</div>

<!-- =========================================================================
     MODALS
     ========================================================================= -->

<!-- MODAL 1: PROFILE VERIFICATION (KYC) FORM -->
<div class="modal fade" id="verifyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light fs-6"><i class="fa-solid fa-shield-halved text-teal me-2"></i> Profile Verification (KYC Form)</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('customer.verification.submit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <p class="small mb-4" style="color: #94a3b8;">Please submit your verified identity and banking details to unlock withdrawal payouts.</p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Full Name (As per PAN)</label>
                            <input type="text" name="full_name" class="form-control exact-input" value="{{ old('full_name', $user->verification->full_name ?? $user->name) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">PAN Card Number</label>
                            <input type="text" name="pan_number" class="form-control exact-input text-uppercase" value="{{ old('pan_number', $user->verification->pan_number ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Upload PAN Card Image</label>
                            <input type="file" name="pan_card_photo" class="form-control exact-input" accept="image/*" {{ $user->verification ? '' : 'required' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Upload Signature Photo</label>
                            <input type="file" name="signature_photo" class="form-control exact-input" accept="image/*" {{ $user->verification ? '' : 'required' }}>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Bank Account Number</label>
                            <input type="text" name="bank_account" class="form-control exact-input" value="{{ old('bank_account', $user->verification->bank_account ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">IFSC Code</label>
                            <input type="text" name="ifsc_code" class="form-control exact-input text-uppercase" value="{{ old('ifsc_code', $user->verification->ifsc_code ?? '') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Phone Number</label>
                            <input type="text" name="phone" class="form-control exact-input" value="{{ old('phone', $user->verification->phone ?? $user->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold" style="color: #94a3b8;">Email ID</label>
                            <input type="email" name="email" class="form-control exact-input" value="{{ old('email', $user->verification->email ?? $user->email) }}" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary border-opacity-25">
                    <button type="button" class="btn btn-dark-outline py-1.5 px-3 small" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-exact-teal py-1.5 px-3">Submit Verification</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL 2: WITHDRAWAL REQUEST MODAL -->
@if($user->isVerified())
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light fs-6"><i class="fa-solid fa-hand-holding-dollar text-teal me-2"></i> Request Withdrawal</h5>
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
                        <label for="amount" class="form-label small fw-semibold" style="color: #94a3b8;">Amount to Withdraw (₹)</label>
                        <input type="number" step="0.01" min="100" max="{{ $user->earning_balance }}" name="amount" id="amount" class="form-control exact-input" style="font-size: 1.15rem; font-weight: 700;" required>
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

<!-- MODAL 3: EARNINGS STATEMENT MODAL -->
<div class="modal fade" id="statementModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="background: #0f172a; border: 1px solid rgba(255,255,255,0.1); border-radius: 16px;">
            <div class="modal-header border-secondary border-opacity-25">
                <h5 class="modal-title fw-bold text-light fs-6"><i class="fa-solid fa-receipt text-teal me-2"></i> Royalty &amp; Earnings Statement</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive" style="max-height: 400px;">
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

@section('scripts')
<script>
    // Neon wave line for Welcome Banner (Matches Reference Image)
    function drawWelcomeWave() {
        const canvas = document.getElementById('welcomeWaveChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        let width = canvas.width = canvas.offsetWidth;
        let height = canvas.height = canvas.offsetHeight;

        window.addEventListener('resize', () => {
            width = canvas.width = canvas.offsetWidth;
            height = canvas.height = canvas.offsetHeight;
        });

        const points = [0.85, 0.8, 0.72, 0.68, 0.75, 0.7, 0.65, 0.68, 0.58, 0.62, 0.5, 0.55, 0.45, 0.5, 0.42, 0.28, 0.15];
        let offset = 0;

        function animate() {
            ctx.clearRect(0, 0, width, height);

            const step = width / (points.length - 1);

            // 1. Subtle glowing area fill underneath the curve
            const areaGrad = ctx.createLinearGradient(0, 0, 0, height);
            areaGrad.addColorStop(0, 'rgba(0, 210, 170, 0.28)');
            areaGrad.addColorStop(0.65, 'rgba(0, 210, 170, 0.06)');
            areaGrad.addColorStop(1, 'rgba(0, 210, 170, 0)');

            ctx.beginPath();
            for (let i = 0; i < points.length; i++) {
                const x = i * step;
                const wave = Math.sin(offset + i * 0.4) * 3.5;
                const y = (points[i] * height) + wave;

                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    const prevX = (i - 1) * step;
                    const prevY = (points[i - 1] * height) + Math.sin(offset + (i - 1) * 0.4) * 3.5;
                    const cpX = (prevX + x) / 2;
                    ctx.quadraticCurveTo(cpX, prevY, x, y);
                }
            }
            ctx.lineTo(width, height);
            ctx.lineTo(0, height);
            ctx.closePath();
            ctx.fillStyle = areaGrad;
            ctx.shadowBlur = 0;
            ctx.fill();

            // 2. Glowing Neon Wave Line
            ctx.beginPath();
            ctx.strokeStyle = '#00d2aa';
            ctx.lineWidth = 2.4;
            ctx.shadowColor = 'rgba(0, 210, 170, 0.95)';
            ctx.shadowBlur = 14;

            for (let i = 0; i < points.length; i++) {
                const x = i * step;
                const wave = Math.sin(offset + i * 0.4) * 3.5;
                const y = (points[i] * height) + wave;

                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    const prevX = (i - 1) * step;
                    const prevY = (points[i - 1] * height) + Math.sin(offset + (i - 1) * 0.4) * 3.5;
                    const cpX = (prevX + x) / 2;
                    ctx.quadraticCurveTo(cpX, prevY, x, y);
                }
            }
            ctx.stroke();

            // 3. Glowing white peak dot at the very end
            const lastIdx = points.length - 1;
            const lastX = lastIdx * step;
            const lastY = (points[lastIdx] * height) + Math.sin(offset + lastIdx * 0.4) * 3.5;

            ctx.beginPath();
            ctx.fillStyle = '#ffffff';
            ctx.shadowColor = '#00d2aa';
            ctx.shadowBlur = 18;
            ctx.arc(lastX, lastY, 4, 0, Math.PI * 2);
            ctx.fill();

            offset += 0.035;
            requestAnimationFrame(animate);
        }

        animate();
    }

    document.addEventListener('DOMContentLoaded', function() {
        drawWelcomeWave();
    });
</script>
@endsection
