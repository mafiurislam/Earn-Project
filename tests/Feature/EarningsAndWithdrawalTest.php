<?php

namespace Tests\Feature;

use App\Models\PasswordResetOtp;
use App\Models\User;
use App\Models\Verification;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EarningsAndWithdrawalTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $verifiedCustomer;

    protected User $unverifiedCustomer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::where('is_admin', true)->first();

        // Create temporary verified customer for tests
        $this->verifiedCustomer = User::create([
            'name' => 'Test Verified User',
            'username' => 'testverified',
            'email' => 'testverified@example.com',
            'phone' => '+91 9999900001',
            'password' => bcrypt('password'),
            'earning_balance' => 600.00,
            'total_earnings' => 1100.00,
            'is_admin' => false,
        ]);

        Verification::create([
            'user_id' => $this->verifiedCustomer->id,
            'full_name' => 'Test Verified User',
            'pan_number' => 'ABCDE1234F',
            'pan_card_photo' => 'verifications/test_pan.png',
            'signature_photo' => 'verifications/test_sig.png',
            'bank_account' => '1234567890',
            'ifsc_code' => 'HDFC0001235',
            'phone' => '+91 9999900001',
            'email' => 'testverified@example.com',
            'status' => 'approved',
        ]);

        // Create temporary unverified customer for tests
        $this->unverifiedCustomer = User::create([
            'name' => 'Test Unverified User',
            'username' => 'testunverified',
            'email' => 'testunverified@example.com',
            'phone' => '+91 9999900002',
            'password' => bcrypt('password'),
            'earning_balance' => 500.00,
            'total_earnings' => 500.00,
            'is_admin' => false,
        ]);
    }

    public function test_admin_can_increase_customer_earnings(): void
    {
        $admin = $this->admin;
        $customer = $this->verifiedCustomer;

        $initialBalance = (float) $customer->earning_balance;
        $initialTotal = (float) $customer->total_earnings;

        $response = $this->actingAs($admin)->post(route('admin.earnings.increase', $customer->id), [
            'amount' => 500.00,
            'note' => 'Bonus test credit',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertEquals($initialBalance + 500.00, (float) $customer->earning_balance);
        $this->assertEquals($initialTotal + 500.00, (float) $customer->total_earnings);

        $this->assertDatabaseHas('earning_transactions', [
            'user_id' => $customer->id,
            'type' => 'credit',
            'amount' => 500.00,
            'note' => 'Bonus test credit',
        ]);
    }

    public function test_admin_can_decrease_customer_earnings(): void
    {
        $admin = $this->admin;
        $customer = $this->verifiedCustomer;

        $initialBalance = (float) $customer->earning_balance;
        $initialTotal = (float) $customer->total_earnings;

        $response = $this->actingAs($admin)->post(route('admin.earnings.decrease', $customer->id), [
            'amount' => 200.00,
            'note' => 'Test deduction',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertEquals($initialBalance - 200.00, (float) $customer->earning_balance);
        $this->assertEquals($initialTotal - 200.00, (float) $customer->total_earnings);

        $this->assertDatabaseHas('earning_transactions', [
            'user_id' => $customer->id,
            'type' => 'debit',
            'amount' => 200.00,
            'note' => 'Test deduction',
        ]);
    }

    public function test_admin_can_update_customer_earnings_directly(): void
    {
        $admin = $this->admin;
        $customer = $this->verifiedCustomer;

        $response = $this->actingAs($admin)->post(route('admin.earnings.update', $customer->id), [
            'earning_balance' => 30000.00,
            'total_earnings' => 45000.00,
            'note' => 'Direct update test',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertEquals(30000.00, (float) $customer->earning_balance);
        $this->assertEquals(45000.00, (float) $customer->total_earnings);
    }

    public function test_unverified_customer_cannot_withdraw(): void
    {
        $customer = $this->unverifiedCustomer;

        $response = $this->actingAs($customer)->post(route('customer.withdrawal.request'), [
            'amount' => 500.00,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_verified_customer_can_withdraw_and_reflects_on_profile(): void
    {
        $customer = $this->verifiedCustomer;
        $balanceBefore = (float) $customer->earning_balance;

        $response = $this->actingAs($customer)->post(route('customer.withdrawal.request'), [
            'amount' => 100.00,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $customer->refresh();
        $this->assertEquals($balanceBefore - 100.00, (float) $customer->earning_balance);

        $withdrawal = Withdrawal::where('user_id', $customer->id)->latest('id')->first();
        $this->assertEquals(100.00, (float) $withdrawal->amount);
        $this->assertEquals('pending', $withdrawal->status);

        // Check customer dashboard displays properly
        $dashResponse = $this->actingAs($customer)->get(route('customer.dashboard'));
        $dashResponse->assertStatus(200);
        $dashResponse->assertSee('TOTAL EARNINGS');
        $dashResponse->assertSee('AVAILABLE BALANCE');
        $dashResponse->assertSee('TOTAL WITHDRAWN');
    }

    public function test_admin_can_approve_withdrawal(): void
    {
        $admin = $this->admin;
        $customer = $this->verifiedCustomer;

        $withdrawal = Withdrawal::create([
            'user_id' => $customer->id,
            'amount' => 150.00,
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post(route('admin.withdrawal.approve', $withdrawal->id), [
            'admin_note' => 'Processed via RTGS Ref #12345',
        ]);

        $response->assertRedirect();
        $withdrawal->refresh();
        $this->assertEquals('approved', $withdrawal->status);
    }

    public function test_admin_reject_refunds_balance(): void
    {
        $admin = $this->admin;
        $customer = $this->verifiedCustomer;

        // Create a pending withdrawal
        $withdrawal = Withdrawal::create([
            'user_id' => $customer->id,
            'amount' => 200.00,
            'status' => 'pending',
        ]);

        $balanceBefore = (float) $customer->earning_balance;

        $response = $this->actingAs($admin)->post(route('admin.withdrawal.reject', $withdrawal->id), [
            'admin_note' => 'Invalid bank details',
        ]);

        $response->assertRedirect();
        $withdrawal->refresh();
        $this->assertEquals('rejected', $withdrawal->status);

        $customer->refresh();
        $this->assertEquals($balanceBefore + 200.00, (float) $customer->earning_balance);
    }

    public function test_customer_registration_succeeds(): void
    {
        $response = $this->post(route('register'), [
            'username' => 'fresh_artist',
            'email' => 'fresh_artist@example.com',
            'phone' => '+91 9876543210',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('customer.dashboard'));
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'username' => 'fresh_artist',
            'email' => 'fresh_artist@example.com',
            'earning_balance' => 0.00,
            'total_earnings' => 0.00,
            'is_admin' => 0,
        ]);
    }

    public function test_owner_admin_login_with_default_credentials(): void
    {
        $response = $this->post(route('admin.login'), [
            'email' => 'admin12345@gmail.com',
            'password' => 'admin12345',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->is_admin);
    }

    public function test_owner_admin_forgot_password_otp_reset_flow(): void
    {
        // 1. Request OTP
        $sendOtpResponse = $this->post(route('password.otp.send'), [
            'email' => 'admin12345@gmail.com',
        ]);
        $sendOtpResponse->assertRedirect(route('password.verify.form'));
        $this->assertEquals('admin12345@gmail.com', session('reset_email'));

        $otpRecord = PasswordResetOtp::where('email', 'admin12345@gmail.com')->latest()->first();
        $this->assertNotNull($otpRecord);

        // 2. Verify OTP
        $verifyResponse = $this->post(route('password.otp.verify'), [
            'otp' => $otpRecord->otp,
        ]);
        $verifyResponse->assertRedirect(route('password.reset.form'));
        $this->assertTrue(session('otp_verified'));

        // 3. Reset Password
        $resetResponse = $this->post(route('password.reset.submit'), [
            'password' => 'newadminpassword123',
            'password_confirmation' => 'newadminpassword123',
        ]);
        $resetResponse->assertRedirect(route('admin.login'));
        $resetResponse->assertSessionHas('success');

        // 4. Test login with new password
        $loginResponse = $this->post(route('admin.login'), [
            'email' => 'admin12345@gmail.com',
            'password' => 'newadminpassword123',
        ]);
        $loginResponse->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }
}
