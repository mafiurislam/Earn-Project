<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminCustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CopyrightClaimLinkController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\VerificationController;
use App\Models\CopyrightClaimLink;
use Illuminate\Support\Facades\Route;

// Public Home / Landing Page
Route::get('/', function () {
    $claimLinks = CopyrightClaimLink::with('user')
        ->orderBy('slot_number', 'asc')
        ->get()
        ->keyBy('slot_number');

    return view('home', compact('claimLinks'));
})->name('home');

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    // Customer Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Admin Login
    Route::get('/admin/login', [AuthController::class, 'showAdminLoginForm'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminLogin']);

    // Forgot Password OTP Flow
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password/otp/send', [AuthController::class, 'sendOtp'])->name('password.otp.send');
    Route::get('/forgot-password/otp/verify', [AuthController::class, 'showVerifyOtpForm'])->name('password.verify.form');
    Route::post('/forgot-password/otp/verify', [AuthController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::get('/forgot-password/reset', [AuthController::class, 'showResetPasswordForm'])->name('password.reset.form');
    Route::post('/forgot-password/reset', [AuthController::class, 'resetPassword'])->name('password.reset.submit');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Customer Dashboard & Profile
    Route::get('/customer/dashboard', [CustomerController::class, 'dashboard'])->name('customer.dashboard');
    Route::get('/customer/copyright-links', [CopyrightClaimLinkController::class, 'index'])->name('customer.copyright_links.index');
    Route::get('/customer/songs', [SongController::class, 'index'])->name('customer.songs.index');
    Route::get('/customer/withdrawals', [CustomerController::class, 'withdrawals'])->name('customer.withdrawals.index');
    Route::get('/customer/profile-info', [CustomerController::class, 'getProfileInfo'])->name('customer.profile_info.get');
    Route::post('/customer/profile-info', [CustomerController::class, 'storeProfileInfo'])->name('customer.profile_info.store');
    Route::post('/customer/autocart-generator/toggle', [CustomerController::class, 'toggleAutocartGenerator'])->name('customer.autocart_generator.toggle');
    Route::post('/customer/profile/photo', [CustomerController::class, 'updateProfilePhoto'])->name('customer.profile.photo');
    Route::post('/customer/verification', [VerificationController::class, 'submitVerification'])->name('customer.verification.submit');
    Route::post('/customer/withdrawal', [CustomerController::class, 'requestWithdrawal'])->name('customer.withdrawal.request');
    Route::post('/customer/copyright-links', [CopyrightClaimLinkController::class, 'store'])->name('customer.copyright_links.store');
    Route::put('/customer/copyright-links/{id}', [CopyrightClaimLinkController::class, 'update'])->name('customer.copyright_links.update');
    Route::delete('/customer/copyright-links/{id}', [CopyrightClaimLinkController::class, 'destroy'])->name('customer.copyright_links.destroy');

    // Customer Song Upload & Management
    Route::post('/customer/songs', [SongController::class, 'store'])->name('customer.songs.store');
    Route::put('/customer/songs/{id}', [SongController::class, 'update'])->name('customer.songs.update');
    Route::delete('/customer/songs/{id}', [SongController::class, 'destroy'])->name('customer.songs.destroy');

    // Admin Dashboard & Control
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/verification/{id}/approve', [AdminController::class, 'approveVerification'])->name('admin.verification.approve');
    Route::post('/admin/verification/{id}/reject', [AdminController::class, 'rejectVerification'])->name('admin.verification.reject');
    Route::post('/admin/withdrawal/{id}/approve', [AdminController::class, 'approveWithdrawal'])->name('admin.withdrawal.approve');
    Route::post('/admin/withdrawal/{id}/reject', [AdminController::class, 'rejectWithdrawal'])->name('admin.withdrawal.reject');
    Route::post('/admin/user/{id}/balance', [AdminController::class, 'updateCustomerBalance'])->name('admin.user.balance.update');
    Route::post('/admin/earnings/{id}/increase', [AdminController::class, 'increaseEarnings'])->name('admin.earnings.increase');
    Route::post('/admin/earnings/{id}/decrease', [AdminController::class, 'decreaseEarnings'])->name('admin.earnings.decrease');
    Route::post('/admin/earnings/{id}/update', [AdminController::class, 'updateEarnings'])->name('admin.earnings.update');
    Route::post('/admin/copyright-links', [CopyrightClaimLinkController::class, 'adminStore'])->name('admin.copyright_links.store');
    Route::put('/admin/copyright-links/{id}', [CopyrightClaimLinkController::class, 'adminUpdate'])->name('admin.copyright_links.update');
    Route::delete('/admin/copyright-links/{id}', [CopyrightClaimLinkController::class, 'adminDestroy'])->name('admin.copyright_links.destroy');

    // Admin Individual Customer Profiles & CRUD
    Route::post('/admin/customers', [AdminCustomerController::class, 'store'])->name('admin.customers.store');
    Route::get('/admin/customers/{id}', [AdminCustomerController::class, 'show'])->name('admin.customers.show');
    Route::put('/admin/customers/{id}', [AdminCustomerController::class, 'update'])->name('admin.customers.update');
    Route::delete('/admin/customers/{id}', [AdminCustomerController::class, 'destroy'])->name('admin.customers.destroy');

    // Admin Song Management for Customers
    Route::post('/admin/customers/{id}/songs', [AdminCustomerController::class, 'storeSong'])->name('admin.customers.songs.store');
    Route::put('/admin/songs/{id}', [AdminCustomerController::class, 'updateSong'])->name('admin.songs.update');
    Route::delete('/admin/songs/{id}', [AdminCustomerController::class, 'destroySong'])->name('admin.songs.destroy');
});

// Fallback Route for public storage files on Hostinger/Shared hosting when symlinks are not available
Route::get('/storage/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(403, 'Unauthorized path traversal attempt.');
    }

    $filePath = storage_path('app/public/'.$path);
    if (! file_exists($filePath)) {
        abort(404, 'File not found.');
    }

    return response()->file($filePath);
})->where('path', '.*')->name('storage.fallback');
