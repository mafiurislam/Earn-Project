@extends('layouts.app')

@section('title', 'Forgot Password - Rajdoot Nivedan Media')

@section('content')
<div class="auth-page-container">

    <!-- Brand Logo Pill -->
    <a href="{{ route('home') }}" class="brand-pill-logo mb-2" aria-label="Rajdoot Nivedan Media">
        <div class="brand-pill-icon">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <div class="brand-pill-text">
            <span class="brand-pill-title">Rajdoot Nivedan</span>
            <span class="brand-pill-subtitle">MEDIA</span>
        </div>
    </a>

    <div class="auth-card">
        
        <div class="auth-icon-badge">
            <i class="fa-solid fa-key"></i>
        </div>

        <h1 class="auth-title">Reset Password</h1>
        <p class="auth-subtitle">Verify your identity and create a new password</p>

        <!-- Step Indicator -->
        <div class="d-flex justify-content-center align-items-center gap-2 mb-4">
            <span class="badge {{ $step == 1 ? 'badge-approved' : 'badge-unverified' }} rounded-pill px-3 py-1">1. Email</span>
            <i class="fa-solid fa-chevron-right text-teal small"></i>
            <span class="badge {{ $step == 2 ? 'badge-approved' : 'badge-unverified' }} rounded-pill px-3 py-1">2. OTP</span>
            <i class="fa-solid fa-chevron-right text-teal small"></i>
            <span class="badge {{ $step == 3 ? 'badge-approved' : 'badge-unverified' }} rounded-pill px-3 py-1">3. Password</span>
        </div>

        @if ($errors->any())
            <div style="background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.4); border-radius: 12px; padding: 12px; margin-bottom: 20px; font-size: 0.85rem; color: #fb7185;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- STEP 1: Enter Email ID -->
        @if($step == 1)
            <form action="{{ route('password.otp.send') }}" method="POST">
                @csrf
                <div class="auth-input-group">
                    <label for="email" class="auth-label">Registered Email ID</label>
                    <div class="auth-input-wrapper">
                        <i class="fa-regular fa-envelope auth-input-icon"></i>
                        <input type="email" name="email" id="email" class="auth-input" value="{{ old('email') }}" required autofocus>
                    </div>
                    <div class="text-white small mt-2">
                        We will send a 6-digit One Time Password (OTP) to your registered email.
                    </div>
                </div>

                <button type="submit" class="btn-auth-submit">
                    Send OTP Code
                </button>
            </form>

        <!-- STEP 2: Enter OTP Code -->
        @elseif($step == 2)
            @if(session('otp_demo'))
                <div class="p-3 rounded-3 mb-3 text-center" style="background: rgba(250, 204, 21, 0.1); border: 1px solid rgba(250, 204, 21, 0.3); color: #facc15;">
                    <small class="fw-bold d-block">DEMO OTP CODE:</small>
                    <span class="fs-4 fw-extrabold text-light tracking-widest">{{ session('otp_demo') }}</span>
                </div>
            @endif

            <form action="{{ route('password.otp.verify') }}" method="POST">
                @csrf
                <div class="auth-input-group text-center">
                    <p class="text-white small mb-2">Enter the 6-digit OTP code sent to <strong>{{ $email }}</strong></p>
                    <input type="text" name="otp" id="otp" class="auth-input text-center fs-3 fw-bold" maxlength="6" required autofocus style="letter-spacing: 0.4rem; padding-left: 14px;">
                </div>

                <button type="submit" class="btn-auth-submit">
                    Verify OTP Code
                </button>
            </form>

        <!-- STEP 3: Set New Password -->
        @elseif($step == 3)
            <form action="{{ route('password.reset.submit') }}" method="POST">
                @csrf

                <div class="auth-input-group">
                    <label for="password" class="auth-label">New Password</label>
                    <div class="auth-input-wrapper">
                        <i class="fa-solid fa-lock auth-input-icon"></i>
                        <input type="password" name="password" id="password" class="auth-input has-toggle" required autofocus>
                        <button type="button" class="auth-password-toggle" onclick="togglePasswordVisibility('password', this)" aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="auth-input-group">
                    <label for="password_confirmation" class="auth-label">Confirm New Password</label>
                    <div class="auth-input-wrapper">
                        <i class="fa-solid fa-lock auth-input-icon"></i>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="auth-input has-toggle" required>
                        <button type="button" class="auth-password-toggle" onclick="togglePasswordVisibility('password_confirmation', this)" aria-label="Toggle password visibility">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn-auth-submit">
                    Set New Password &amp; Login
                </button>
            </form>
        @endif

        <div style="margin-top: 24px; font-size: 0.88rem; color: #ffffff; text-align: center;">
            Remember your password? <a href="{{ route('login') }}" style="color: var(--teal); font-weight: 700; text-decoration: none;">Return to Login</a>
        </div>

    </div>

</div>
@endsection

@section('scripts')
<script>
    function togglePasswordVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        if (!input) return;
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) {
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        } else {
            input.type = 'password';
            if (icon) {
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }
</script>
@endsection
