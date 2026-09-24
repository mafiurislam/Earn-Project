<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rajdoot Nivedan Media - Global Music Distribution & Live Earnings')</title>
    <meta name="description" content="Turn your music streams into steady live royalties with Rajdoot Nivedan Media.">

    <!-- Google Fonts: Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS (Exact match to reference designs) -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @yield('styles')
</head>
<body>

    <!-- Global Header -->
    <header class="site-header">
        <div class="container-fluid px-4 header-container">
            
            <!-- Left: Brand Logo (Exact match to homepage reference Image 1 & inner pages) -->
            @if(Request::is('/') || Request::routeIs('home'))
                <a href="{{ route('home') }}" class="brand-home-logo" aria-label="Rajdoot Nivedan Media">
                    <img src="{{ asset('images/logo.png') }}" alt="Rajdoot Nivedan">
                </a>
            @else
                <a href="{{ route('home') }}" class="brand-pill-logo" aria-label="Rajdoot Nivedan Media">
                    <div class="brand-pill-icon">
                        <img src="{{ asset('images/logo.png') }}" alt="Rajdoot Nivedan Logo">
                    </div>
                    <div class="brand-pill-text">
                        <span class="brand-pill-title">Rajdoot Nivedan</span>
                        <span class="brand-pill-subtitle">MEDIA</span>
                    </div>
                </a>
            @endif

            <!-- Right: Actions & User Controls -->
            <div class="d-flex align-items-center gap-2.5">
                @auth
                    @if(Auth::user()->is_admin || Request::is('admin*'))
                        <!-- Customer View Quick Link (Matches Admin Console Reference Image) -->
                        <a href="{{ route('customer.dashboard') }}" class="btn-customer-view text-decoration-none">
                            <i class="fa-solid fa-table-cells-large text-teal"></i>
                            <span>Customer view</span>
                        </a>
                    @endif

                    <!-- User Dropdown with Circular Avatar & Initials Badge -->
                    <div class="dropdown">
                        <button class="btn p-0 border-0 bg-transparent" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="header-avatar-circle" title="{{ Auth::user()->name }}">
                                @php
                                    $initial = (Auth::user()->is_admin || Request::is('admin*')) ? 'U' : strtoupper(substr(Auth::user()->name, 0, 1));
                                @endphp
                                <span>{{ $initial }}</span>
                                <div class="header-avatar-camera"><i class="fa-solid fa-camera"></i></div>
                            </div>
                        </button>
                        @php
                            $isProfileComplete = Auth::user()->isProfileComplete();
                        @endphp
                        <div class="dropdown-menu dropdown-menu-end user-profile-dropdown">
                            <!-- User Identity Header (Exact match to reference image) -->
                            <div class="user-profile-header">
                                <div class="user-profile-name">{{ Auth::user()->name }}</div>
                                <div class="user-profile-email">{{ Auth::user()->email }}</div>
                            </div>
                            <div class="user-profile-divider"></div>

                            @if(Auth::user()->is_admin || Request::is('admin*'))
                                <a class="user-profile-item" href="{{ route('admin.dashboard') }}">
                                    <span class="item-icon"><i class="fa-solid fa-shield-halved text-warning"></i></span>
                                    <span>Admin Console</span>
                                </a>
                                <a class="user-profile-item" href="#" data-bs-toggle="modal" data-bs-target="#adminCreateCustomerModal">
                                    <span class="item-icon"><i class="fa-solid fa-user-plus text-teal"></i></span>
                                    <span>Add New Customer</span>
                                </a>
                                <a class="user-profile-item" href="#" data-bs-toggle="modal" data-bs-target="#adminAddClaimLinkModal">
                                    <span class="item-icon"><i class="fa-solid fa-link text-teal"></i></span>
                                    <span>Add Claim Link</span>
                                </a>
                                <div class="user-profile-divider"></div>
                            @endif

                            <!-- Option 1: Customer Dashboard -->
                            <a class="user-profile-item" href="{{ route('customer.dashboard') }}">
                                <span class="item-icon"><i class="fa-solid fa-gauge"></i></span>
                                <span>Customer Dashboard</span>
                            </a>

                            <div class="user-profile-divider"></div>

                            <!-- Option 2: Profile Info (With Red Dot / Green Dot Status Indicator) -->
                            <button type="button" class="user-profile-item" data-bs-toggle="modal" data-bs-target="#customerProfileInfoModal">
                                <span class="item-icon"><i class="fa-solid fa-id-card"></i></span>
                                <span class="d-inline-flex align-items-center justify-content-between flex-grow-1">
                                    <span>Profile Info</span>
                                    <span id="profileDropdownIndicator" 
                                          class="profile-status-dot {{ $isProfileComplete ? 'dot-green' : 'dot-red' }}" 
                                          title="{{ $isProfileComplete ? 'Profile Complete (Green)' : 'Profile Incomplete (Red)' }}"></span>
                                </span>
                            </button>

                            <!-- Option 3: Copyright Claim Remove Links -->
                            <a class="user-profile-item" href="{{ route('customer.copyright_links.index') }}">
                                <span class="item-icon"><i class="fa-solid fa-link"></i></span>
                                <span>Copyright Claim Remove Links</span>
                            </a>

                            <!-- Option 4: Upload Song -->
                            <a class="user-profile-item" href="{{ route('customer.songs.index') }}">
                                <span class="item-icon"><i class="fa-solid fa-music"></i></span>
                                <span>Upload Song</span>
                            </a>

                            <!-- Option 5: Upload Autocart Generator (With On/Off Toggle Switch) -->
                            <div class="user-profile-item d-flex align-items-center justify-content-between" style="cursor: pointer;" onclick="document.getElementById('autocartToggleInput').click();">
                                <div class="d-flex align-items-center">
                                    <span class="item-icon"><i class="fa-solid fa-wand-magic-sparkles text-teal"></i></span>
                                    <span>Upload Autocart Generator</span>
                                </div>
                                <div class="d-flex align-items-center gap-2 ms-2" onclick="event.stopPropagation();">
                                    <span id="autocartStatusText" class="small fw-bold {{ Auth::user()->autocart_generator_enabled ? 'text-success' : 'text-danger' }}" style="font-size: 0.74rem;">
                                        {{ Auth::user()->autocart_generator_enabled ? 'ON' : 'OFF' }}
                                    </span>
                                    <label class="custom-switch-toggle mb-0" title="Turn Autocart Generator ON/OFF">
                                        <input type="checkbox" id="autocartToggleInput" {{ Auth::user()->autocart_generator_enabled ? 'checked' : '' }} onchange="toggleAutocartGenerator(this.checked)">
                                        <span class="switch-slider"></span>
                                    </label>
                                </div>
                            </div>

                            <!-- Option 5: Withdrawal History -->
                            <a class="user-profile-item" href="{{ route('customer.withdrawals.index') }}">
                                <span class="item-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>
                                <span>Withdrawal History</span>
                            </a>

                            <div class="user-profile-divider"></div>

                            <!-- Option 6: Logout (Red accent matching reference image) -->
                            <form action="{{ route('logout') }}" method="POST" class="m-0 p-0">
                                @csrf
                                <button type="submit" class="user-profile-item logout-item">
                                    <span class="item-icon"><i class="fa-solid fa-right-from-bracket"></i></span>
                                    <span>Logout</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Ghost User Outline Icon (Opens Owner Admin Login Panel) -->
                    <a href="{{ route('admin.login') }}" class="header-icon-btn" title="Owner Admin Login">
                        <i class="fa-regular fa-user"></i>
                    </a>

                    <!-- Green Pill "Customer Login" Button (Matches Image 1) -->
                    <a href="{{ route('login') }}" class="btn-header-login">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i>
                        <span>Customer Login</span>
                    </a>
                @endauth
            </div>

        </div>
    </header>

    <!-- Flash Notification Toasts (Floating, Non-intrusive) -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1200;">
        @if(session('success'))
            <div class="toast show align-items-center text-white bg-dark border border-teal shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; background: #0f172a !important;">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check text-teal fs-5"></i>
                        <span class="small fw-semibold">{{ session('success') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="toast show align-items-center text-white bg-dark border border-danger shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; background: #0f172a !important;">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation text-danger fs-5"></i>
                        <span class="small fw-semibold">{{ session('error') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="toast show align-items-center text-white bg-dark border border-info shadow-lg mb-2" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; background: #0f172a !important;">
                <div class="d-flex">
                    <div class="toast-body d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-info text-info fs-5"></i>
                        <span class="small fw-semibold">{{ session('info') }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow-1">
        @yield('content')
    </main>

    <!-- Footer (Omitted on Customer and Admin Dashboards to match reference images) -->
    @unless(Request::is('customer*') || Request::is('admin*'))
        <footer class="py-4 border-top border-secondary border-opacity-25 mt-auto" style="background: rgba(8, 12, 20, 0.95);">
            <div class="container-fluid px-4">
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-white small">
                    <div>
                        &copy; 2026 Rajdoot Nivedan Media. All rights reserved.
                    </div>
                    <div class="fw-semibold text-white">
                        Distribute &middot; Earn &middot; Grow
                    </div>
                </div>
            </div>
        </footer>
    @endunless

    @auth
        <!-- Customer Profile Info Modal -->
        <div class="modal fade modal-profile-info" id="customerProfileInfoModal" tabindex="-1" aria-labelledby="customerProfileInfoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-id-card text-teal fs-5"></i>
                            <h5 class="modal-title text-light fw-bold mb-0" id="customerProfileInfoModalLabel">Customer Profile Info</h5>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span id="profileModalStatusBadge" class="badge-profile-status {{ Auth::user()->isProfileComplete() ? 'complete' : 'incomplete' }}">
                                <i class="fa-solid {{ Auth::user()->isProfileComplete() ? 'fa-circle-check' : 'fa-circle-exclamation' }}"></i>
                                <span id="profileModalStatusText">{{ Auth::user()->isProfileComplete() ? 'Completed' : 'Incomplete' }}</span>
                            </span>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <form id="customerProfileInfoForm" action="{{ route('customer.profile_info.store') }}" method="POST" novalidate>
                        @csrf
                        <div class="modal-body">
                            <div class="small mb-3 text-secondary" style="font-size: 0.85rem;">
                                Please fill in all 4 required details below. All fields must be completed before submission.
                            </div>

                            <div id="profileFormAlert" class="alert alert-danger d-none py-2 px-3 small rounded-3" role="alert"></div>

                            <!-- 1. Owner Name -->
                            <div class="mb-3">
                                <label for="profile_owner_name" class="form-label text-light fw-bold small mb-1.5">
                                    Owner Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-dark" 
                                       id="profile_owner_name" 
                                       name="owner_name" 
                                       value="{{ old('owner_name', Auth::user()->profileInfo->owner_name ?? '') }}" 
                                       placeholder="Enter owner full name" 
                                       required>
                                <div class="invalid-feedback" id="feedback_owner_name">Owner Name is required.</div>
                            </div>

                            <!-- 2. YouTube Channel Name -->
                            <div class="mb-3">
                                <label for="profile_channel_name" class="form-label text-light fw-bold small mb-1.5">
                                    YouTube Channel Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-dark" 
                                       id="profile_channel_name" 
                                       name="channel_name" 
                                       value="{{ old('channel_name', Auth::user()->profileInfo->channel_name ?? '') }}" 
                                       placeholder="Enter YouTube channel name" 
                                       required>
                                <div class="invalid-feedback" id="feedback_channel_name">YouTube Channel Name is required.</div>
                            </div>

                            <!-- 3. YouTube Link -->
                            <div class="mb-3">
                                <label for="profile_youtube_link" class="form-label text-light fw-bold small mb-1.5">
                                    YouTube Link <span class="text-danger">*</span>
                                </label>
                                <input type="url" 
                                       class="form-control form-control-dark" 
                                       id="profile_youtube_link" 
                                       name="youtube_link" 
                                       value="{{ old('youtube_link', Auth::user()->profileInfo->youtube_link ?? '') }}" 
                                       placeholder="https://youtube.com/@channel or channel link" 
                                       required>
                                <div class="invalid-feedback" id="feedback_youtube_link">YouTube Link is required.</div>
                            </div>

                            <!-- 4. Label Name -->
                            <div class="mb-3">
                                <label for="profile_label_name" class="form-label text-light fw-bold small mb-1.5">
                                    Label Name <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-dark" 
                                       id="profile_label_name" 
                                       name="label_name" 
                                       value="{{ old('label_name', Auth::user()->profileInfo->label_name ?? '') }}" 
                                       placeholder="Enter record label name" 
                                       required>
                                <div class="invalid-feedback" id="feedback_label_name">Label Name is required.</div>
                            </div>

                            <div class="p-2.5 rounded-3 d-flex align-items-center justify-content-between" style="background: rgba(255, 255, 255, 0.03); border: 1px dashed rgba(255, 255, 255, 0.1);">
                                <span class="small text-secondary" style="font-size: 0.8rem;">Completion Progress:</span>
                                <span id="profileFieldsProgress" class="small fw-bold text-teal" style="font-size: 0.82rem;">0/4 completed</span>
                            </div>
                        </div>
                        <div class="modal-footer d-flex align-items-center justify-content-between">
                            <button type="button" class="btn btn-dark-outline rounded-3 py-2 px-3.5 small fw-semibold" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" id="btnSubmitProfile" class="btn-submit-profile" disabled>
                                <i class="fa-solid fa-check"></i>
                                <span>Submit</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @auth
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('customerProfileInfoForm');
            if (!form) return;

            const fields = [
                document.getElementById('profile_owner_name'),
                document.getElementById('profile_channel_name'),
                document.getElementById('profile_youtube_link'),
                document.getElementById('profile_label_name')
            ];
            const submitBtn = document.getElementById('btnSubmitProfile');
            const progressEl = document.getElementById('profileFieldsProgress');
            const indicator = document.getElementById('profileDropdownIndicator');
            const statusBadge = document.getElementById('profileModalStatusBadge');
            const formAlert = document.getElementById('profileFormAlert');

            function checkFormValidity() {
                let completedCount = 0;
                let allValid = true;

                fields.forEach(field => {
                    if (!field) return;
                    const val = field.value.trim();
                    if (val.length > 0) {
                        completedCount++;
                        field.classList.remove('is-invalid');
                        field.classList.add('is-valid');
                    } else {
                        allValid = false;
                        field.classList.remove('is-valid');
                    }
                });

                if (progressEl) {
                    progressEl.textContent = `${completedCount}/4 completed`;
                    if (completedCount === 4) {
                        progressEl.className = 'small fw-bold text-teal';
                    } else {
                        progressEl.className = 'small fw-bold text-warning';
                    }
                }

                if (submitBtn) {
                    submitBtn.disabled = !allValid;
                }

                return allValid;
            }

            fields.forEach(field => {
                if (!field) return;
                field.addEventListener('input', function () {
                    if (this.value.trim().length > 0) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    } else {
                        this.classList.remove('is-valid');
                    }
                    checkFormValidity();
                });

                field.addEventListener('blur', function () {
                    if (this.value.trim().length === 0) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                    checkFormValidity();
                });
            });

            // Initial check on load and when modal opens
            checkFormValidity();
            const modalEl = document.getElementById('customerProfileInfoModal');
            if (modalEl) {
                modalEl.addEventListener('shown.bs.modal', function () {
                    checkFormValidity();
                });
            }

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                let hasError = false;
                fields.forEach(field => {
                    if (!field) return;
                    if (field.value.trim().length === 0) {
                        field.classList.add('is-invalid');
                        hasError = true;
                    }
                });

                if (hasError || !checkFormValidity()) {
                    if (formAlert) {
                        formAlert.textContent = 'Please complete all 4 required fields before submitting.';
                        formAlert.classList.remove('d-none');
                    }
                    return;
                }

                if (formAlert) {
                    formAlert.classList.add('d-none');
                }

                // AJAX submission
                submitBtn.disabled = true;
                const origBtnHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

                const formData = new FormData(form);

                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => { throw err; });
                    }
                    return response.json();
                })
                .then(data => {
                    submitBtn.innerHTML = '<i class="fa-solid fa-check"></i> Saved!';

                    // Update Dropdown status indicator to GREEN
                    if (indicator) {
                        indicator.classList.remove('dot-red');
                        indicator.classList.add('dot-green');
                        indicator.title = 'Profile Complete (Green)';
                    }

                    // Update modal status badge
                    if (statusBadge) {
                        statusBadge.className = 'badge-profile-status complete';
                        statusBadge.innerHTML = '<i class="fa-solid fa-circle-check"></i> <span>Completed</span>';
                    }

                    setTimeout(() => {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        submitBtn.innerHTML = origBtnHtml;
                        submitBtn.disabled = false;
                    }, 500);
                })
                .catch(err => {
                    submitBtn.innerHTML = origBtnHtml;
                    submitBtn.disabled = false;
                    if (formAlert) {
                        let msg = 'Failed to submit profile. Please ensure all 4 fields are filled.';
                        if (err && err.errors) {
                            msg = Object.values(err.errors).flat().join('<br>');
                        } else if (err && err.message) {
                            msg = err.message;
                        }
                        formAlert.innerHTML = msg;
                        formAlert.classList.remove('d-none');
                    }
                });
            });

            // Autocart Generator Toggle Handler
            window.toggleAutocartGenerator = function(enabled) {
                const statusText = document.getElementById('autocartStatusText');
                const toggleInput = document.getElementById('autocartToggleInput');

                if (statusText) {
                    statusText.textContent = enabled ? 'ON' : 'OFF';
                    statusText.className = 'small fw-bold ' + (enabled ? 'text-success' : 'text-danger');
                }

                fetch('{{ route('customer.autocart_generator.toggle') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ enabled: enabled })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success && statusText) {
                        statusText.textContent = data.status_text;
                        statusText.className = 'small fw-bold ' + (data.enabled ? 'text-success' : 'text-danger');
                    }
                })
                .catch(err => {
                    console.error('Error toggling autocart generator:', err);
                    if (toggleInput) toggleInput.checked = !enabled;
                    if (statusText) {
                        statusText.textContent = (!enabled) ? 'ON' : 'OFF';
                        statusText.className = 'small fw-bold ' + ((!enabled) ? 'text-success' : 'text-danger');
                    }
                });
            };
        });
    </script>
    @endauth
    @yield('scripts')
</body>
</html>
