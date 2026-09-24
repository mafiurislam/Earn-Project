<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Admin Login - Rajdoot Nivedan Media</title>
    <meta name="description" content="Welcome to Owner Admin Login Panel - Rajdoot Nivedan Media.">

    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
</head>
<body class="auth-page-container">

    <!-- Centered Brand Logo Pill -->
    <a href="{{ route('home') }}" class="brand-pill-logo" aria-label="Rajdoot Nivedan Media">
        <div class="brand-pill-icon">
            <img src="{{ asset('images/logo.png') }}" alt="Logo">
        </div>
        <div class="brand-pill-text">
            <span class="brand-pill-title">Rajdoot Nivedan</span>
            <span class="brand-pill-subtitle">MEDIA</span>
        </div>
    </a>

    <!-- Owner Admin Login Card -->
    <div class="auth-card">
        
        <!-- Shield Icon Badge -->
        <div class="auth-icon-badge">
            <i class="fa-solid fa-user-shield"></i>
        </div>

        <h1 class="auth-title" style="font-size: 1.55rem; line-height: 1.3;">Welcome to Owner Admin Login Panel</h1>
        <p class="auth-subtitle">Sign in to manage customers, verifications, and live earnings</p>

        @if(session('success'))
            <div style="background: rgba(0, 210, 170, 0.15); border: 1px solid rgba(0, 210, 170, 0.4); border-radius: 12px; padding: 12px; margin-bottom: 20px; font-size: 0.85rem; color: #00d2aa;">
                {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div style="background: rgba(56, 189, 248, 0.15); border: 1px solid rgba(56, 189, 248, 0.4); border-radius: 12px; padding: 12px; margin-bottom: 20px; font-size: 0.85rem; color: #38bdf8;">
                {{ session('info') }}
            </div>
        @endif

        @if ($errors->any())
            <div style="background: rgba(244, 63, 94, 0.15); border: 1px solid rgba(244, 63, 94, 0.4); border-radius: 12px; padding: 12px; margin-bottom: 20px; font-size: 0.85rem; color: #fb7185;">
                <ul style="margin: 0; padding-left: 18px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST">
            @csrf

            <!-- Admin Email ID -->
            <div class="auth-input-group">
                <label for="email" class="auth-label">Owner Email ID</label>
                <div class="auth-input-wrapper">
                    <i class="fa-regular fa-envelope auth-input-icon"></i>
                    <input type="email" name="email" id="email" class="auth-input" value="{{ old('email') }}" required autofocus>
                </div>
            </div>

            <!-- Admin Password with Forgot Password link -->
            <div class="auth-input-group">
                <div class="d-flex justify-content-between align-items-center mb-1" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label for="password" class="auth-label" style="margin-bottom: 0;">Password</label>
                    <a href="{{ route('password.request') }}" style="color: var(--teal); font-size: 0.82rem; text-decoration: none; font-weight: 600;">Forgot password?</a>
                </div>
                <div class="auth-input-wrapper">
                    <i class="fa-solid fa-lock auth-input-icon"></i>
                    <input type="password" name="password" id="password" class="auth-input has-toggle" required>
                    <button type="button" class="auth-password-toggle" onclick="togglePasswordVisibility('password', this)" aria-label="Toggle password visibility">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-auth-submit">
                Log in
            </button>
        </form>

        <div style="margin-top: 24px; font-size: 0.88rem; color: #ffffff; text-align: center;">
            Customer? <a href="{{ route('login') }}" style="color: var(--teal); font-weight: 700; text-decoration: none;">Customer Login</a>
        </div>

    </div>

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
</body>
</html>
