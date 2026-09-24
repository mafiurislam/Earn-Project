<?php

namespace App\Http\Controllers;

use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Customer Registration
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'username' => $validated['username'],
            'name' => ! empty($validated['name']) ? $validated['name'] : $validated['username'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'earning_balance' => 0.00,
            'total_earnings' => 0.00,
            'is_admin' => false,
        ]);

        Auth::login($user);

        return redirect()->route('customer.dashboard')->with('success', 'Registration successful! Welcome to Rajdoot Nivedan Media.');
    }

    // Customer Login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('customer.dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    // Admin Login
    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function adminLogin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            if (Auth::user()->is_admin) {
                $request->session()->regenerate();

                return redirect()->route('admin.dashboard')->with('success', 'Welcome to Main Admin Portal');
            } else {
                Auth::logout();

                return back()->withErrors(['email' => 'Unauthorized access. Admin privileges required.']);
            }
        }

        return back()->withErrors([
            'email' => 'Invalid Admin Credentials.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Logged out successfully.');
    }

    // --- Forgot Password OTP Workflow ---

    // Step 1: Show Email Input Form
    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password', ['step' => 1]);
    }

    // Step 1 Submission: Generate OTP
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            return back()->withErrors(['email' => 'No account found with this email address.']);
        }

        // Generate 6-digit OTP code
        $otpCode = str_pad(mt_rand(100000, 999999), 6, '0', STR_PAD_LEFT);

        PasswordResetOtp::where('email', $request->email)->delete();

        PasswordResetOtp::create([
            'email' => $request->email,
            'otp' => $otpCode,
            'expires_at' => now()->addMinutes(15),
        ]);

        session(['reset_email' => $request->email]);

        return redirect()->route('password.verify.form')->with('otp_demo', $otpCode)->with('info', "OTP code sent to {$request->email}! (Your OTP: {$otpCode})");
    }

    // Step 2: Show OTP Verification Form
    public function showVerifyOtpForm()
    {
        if (! session('reset_email')) {
            return redirect()->route('password.request');
        }

        return view('auth.forgot-password', ['step' => 2, 'email' => session('reset_email')]);
    }

    // Step 2 Submission: Verify OTP Code
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|numeric|digits:6',
        ]);

        $email = session('reset_email');
        if (! $email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expired. Please start again.']);
        }

        $record = PasswordResetOtp::where('email', $email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if (! $record) {
            return back()->withErrors(['otp' => 'Invalid or expired OTP code. Please try again.']);
        }

        session(['otp_verified' => true]);

        return redirect()->route('password.reset.form')->with('success', 'OTP verified successfully. Set your new password.');
    }

    // Step 3: Show Reset Password Form
    public function showResetPasswordForm()
    {
        if (! session('reset_email') || ! session('otp_verified')) {
            return redirect()->route('password.request');
        }

        return view('auth.forgot-password', ['step' => 3, 'email' => session('reset_email')]);
    }

    // Step 3 Submission: Save New Password
    public function resetPassword(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $email = session('reset_email');
        if (! $email || ! session('otp_verified')) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expired. Please try again.']);
        }

        $user = User::where('email', $email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        // Clear OTP record and session
        PasswordResetOtp::where('email', $email)->delete();
        session()->forget(['reset_email', 'otp_verified']);

        if ($user->is_admin) {
            return redirect()->route('admin.login')->with('success', 'Password reset successfully! You can now log in to the Owner Admin Login Panel with your new password.');
        }

        return redirect()->route('login')->with('success', 'Password reset successfully! You can now log in with your new password.');
    }
}
