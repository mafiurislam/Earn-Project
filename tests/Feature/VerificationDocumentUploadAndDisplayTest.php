<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Verification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class VerificationDocumentUploadAndDisplayTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Owner',
            'username' => 'adminowner',
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Amit Sharma',
            'username' => 'amitsharma',
            'email' => 'amit@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
            'earning_balance' => 500.00,
        ]);
    }

    public function test_customer_can_upload_pan_and_signature_images(): void
    {
        Storage::fake('public');

        $panFile = UploadedFile::fake()->image('my_pan_card.jpg', 800, 600);
        $sigFile = UploadedFile::fake()->image('my_signature.png', 400, 200);

        $response = $this->actingAs($this->customer)->post(route('customer.verification.submit'), [
            'full_name' => 'Amit Sharma',
            'pan_number' => 'ABCDE1234F',
            'pan_card_photo' => $panFile,
            'signature_photo' => $sigFile,
            'bank_account' => '123456789012',
            'ifsc_code' => 'HDFC0001234',
            'phone' => '+91 9876543210',
            'email' => 'amit@example.com',
        ]);

        $response->assertSessionHas('success');

        $verification = Verification::where('user_id', $this->customer->id)->first();
        $this->assertNotNull($verification);
        $this->assertEquals('pending', $verification->status);
        $this->assertNotNull($verification->pan_card_photo);
        $this->assertNotNull($verification->signature_photo);

        // Assert files exist in public storage
        Storage::disk('public')->assertExists($verification->pan_card_photo);
        Storage::disk('public')->assertExists($verification->signature_photo);

        // Assert URL accessors return valid non-empty URLs
        $this->assertNotEmpty($verification->pan_card_photo_url);
        $this->assertNotEmpty($verification->signature_photo_url);
        $this->assertStringContainsString($verification->pan_card_photo, $verification->pan_card_photo_url);
        $this->assertStringContainsString($verification->signature_photo, $verification->signature_photo_url);
    }

    public function test_uploaded_documents_are_displayed_in_admin_dashboard_and_customer_show(): void
    {
        Storage::fake('public');

        $panFile = UploadedFile::fake()->image('custom_pan.jpg', 600, 400);
        $sigFile = UploadedFile::fake()->image('custom_sig.png', 300, 150);

        $this->actingAs($this->customer)->post(route('customer.verification.submit'), [
            'full_name' => 'Amit Sharma',
            'pan_number' => 'ABCDE1234F',
            'pan_card_photo' => $panFile,
            'signature_photo' => $sigFile,
            'bank_account' => '123456789012',
            'ifsc_code' => 'HDFC0001234',
            'phone' => '+91 9876543210',
            'email' => 'amit@example.com',
        ]);

        $verification = $this->customer->fresh()->verification;

        // 1. Check Admin Dashboard view
        $adminDashboard = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $adminDashboard->assertStatus(200);
        $adminDashboard->assertSee($verification->pan_card_photo_url, false);
        $adminDashboard->assertSee($verification->signature_photo_url, false);
        $adminDashboard->assertSee('PAN Card Image');
        $adminDashboard->assertSee('Signature Photo');

        // 2. Check Admin Customer Profile Show view
        $customerShow = $this->actingAs($this->admin)->get(route('admin.customers.show', $this->customer->id));
        $customerShow->assertStatus(200);
        $customerShow->assertSee($verification->pan_card_photo_url, false);
        $customerShow->assertSee($verification->signature_photo_url, false);
        $customerShow->assertSee('Uploaded Verification Documents');
    }

    public function test_customer_can_update_verification_text_details_without_reuploading(): void
    {
        Storage::fake('public');

        $panFile = UploadedFile::fake()->image('initial_pan.jpg', 600, 400);
        $sigFile = UploadedFile::fake()->image('initial_sig.png', 300, 150);

        $this->actingAs($this->customer)->post(route('customer.verification.submit'), [
            'full_name' => 'Amit Sharma',
            'pan_number' => 'ABCDE1234F',
            'pan_card_photo' => $panFile,
            'signature_photo' => $sigFile,
            'bank_account' => '123456789012',
            'ifsc_code' => 'HDFC0001234',
            'phone' => '+91 9876543210',
            'email' => 'amit@example.com',
        ]);

        $origVerification = $this->customer->fresh()->verification;
        $origPan = $origVerification->pan_card_photo;
        $origSig = $origVerification->signature_photo;

        // Update bank details without sending files
        $response = $this->actingAs($this->customer)->post(route('customer.verification.submit'), [
            'full_name' => 'Amit Sharma Updated',
            'pan_number' => 'ABCDE1234F',
            'bank_account' => '999988887777',
            'ifsc_code' => 'SBIN0001111',
            'phone' => '+91 9876543210',
            'email' => 'amit@example.com',
        ]);

        $response->assertSessionHas('success');

        $updated = $this->customer->fresh()->verification;
        $this->assertEquals('Amit Sharma Updated', $updated->full_name);
        $this->assertEquals('999988887777', $updated->bank_account);
        $this->assertEquals($origPan, $updated->pan_card_photo);
        $this->assertEquals($origSig, $updated->signature_photo);
    }

    public function test_admin_can_download_customer_pan_card_and_signature(): void
    {
        Storage::fake('public');

        $panFile = UploadedFile::fake()->image('amit_pan.jpg', 600, 400);
        $sigFile = UploadedFile::fake()->image('amit_sig.png', 300, 150);

        $this->actingAs($this->customer)->post(route('customer.verification.submit'), [
            'full_name' => 'Amit Sharma',
            'pan_number' => 'ABCDE1234F',
            'pan_card_photo' => $panFile,
            'signature_photo' => $sigFile,
            'bank_account' => '123456789012',
            'ifsc_code' => 'HDFC0001234',
            'phone' => '+91 9876543210',
            'email' => 'amit@example.com',
        ]);

        $verification = $this->customer->fresh()->verification;

        // 1. Admin can download PAN card via admin.verification.download_pan
        $panDownloadResponse = $this->actingAs($this->admin)->get(route('admin.verification.download_pan', $verification->id));
        $panDownloadResponse->assertStatus(200);
        $this->assertStringContainsString('pan-card', (string) $panDownloadResponse->headers->get('content-disposition'));

        // 2. Admin can download PAN card via admin.customers.download_pan
        $custPanResponse = $this->actingAs($this->admin)->get(route('admin.customers.download_pan', $this->customer->id));
        $custPanResponse->assertStatus(200);
        $this->assertStringContainsString('pan-card', (string) $custPanResponse->headers->get('content-disposition'));

        // 3. Admin can download Signature via admin.verification.download_signature
        $sigDownloadResponse = $this->actingAs($this->admin)->get(route('admin.verification.download_signature', $verification->id));
        $sigDownloadResponse->assertStatus(200);
        $this->assertStringContainsString('signature', (string) $sigDownloadResponse->headers->get('content-disposition'));

        // 4. Admin can download Signature via admin.customers.download_signature
        $custSigResponse = $this->actingAs($this->admin)->get(route('admin.customers.download_signature', $this->customer->id));
        $custSigResponse->assertStatus(200);
        $this->assertStringContainsString('signature', (string) $custSigResponse->headers->get('content-disposition'));
    }

    public function test_admin_dashboard_and_customer_show_pages_contain_document_modals_and_download_links(): void
    {
        Storage::fake('public');

        $panFile = UploadedFile::fake()->image('custom_pan.jpg', 600, 400);
        $sigFile = UploadedFile::fake()->image('custom_sig.png', 300, 150);

        $this->actingAs($this->customer)->post(route('customer.verification.submit'), [
            'full_name' => 'Amit Sharma',
            'pan_number' => 'ABCDE1234F',
            'pan_card_photo' => $panFile,
            'signature_photo' => $sigFile,
            'bank_account' => '123456789012',
            'ifsc_code' => 'HDFC0001234',
            'phone' => '+91 9876543210',
            'email' => 'amit@example.com',
        ]);

        $verification = $this->customer->fresh()->verification;

        // Admin dashboard view
        $dashboardResponse = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $dashboardResponse->assertStatus(200);
        $dashboardResponse->assertSee("adminCustomerKycModal{$this->customer->id}", false);
        $dashboardResponse->assertSee('KYC Docs');
        $dashboardResponse->assertSee(route('admin.verification.download_pan', $verification->id));
        $dashboardResponse->assertSee(route('admin.verification.download_signature', $verification->id));

        // Customer show view
        $showResponse = $this->actingAs($this->admin)->get(route('admin.customers.show', $this->customer->id));
        $showResponse->assertStatus(200);
        $showResponse->assertSee(route('admin.verification.download_pan', $verification->id));
        $showResponse->assertSee(route('admin.verification.download_signature', $verification->id));
        $showResponse->assertSee('Download PAN');
        $showResponse->assertSee('Download Signature');
    }

    public function test_unauthorized_user_cannot_download_verification_documents(): void
    {
        Storage::fake('public');

        $panFile = UploadedFile::fake()->image('secure_pan.jpg', 600, 400);
        $sigFile = UploadedFile::fake()->image('secure_sig.png', 300, 150);

        $this->actingAs($this->customer)->post(route('customer.verification.submit'), [
            'full_name' => 'Amit Sharma',
            'pan_number' => 'ABCDE1234F',
            'pan_card_photo' => $panFile,
            'signature_photo' => $sigFile,
            'bank_account' => '123456789012',
            'ifsc_code' => 'HDFC0001234',
            'phone' => '+91 9876543210',
            'email' => 'amit@example.com',
        ]);

        $verification = $this->customer->fresh()->verification;

        // Another non-admin customer
        $otherCustomer = User::create([
            'name' => 'Other Customer',
            'username' => 'othercust',
            'email' => 'other@example.com',
            'password' => bcrypt('password123'),
            'is_admin' => false,
        ]);

        // Attempt as another customer -> forbidden (403)
        $this->actingAs($otherCustomer)->get(route('admin.verification.download_pan', $verification->id))
            ->assertStatus(403);
        $this->actingAs($otherCustomer)->get(route('admin.verification.download_signature', $verification->id))
            ->assertStatus(403);

        // Attempt as guest -> redirect to login (302)
        auth()->logout();
        $this->get(route('admin.verification.download_pan', $verification->id))
            ->assertRedirect(route('login'));
        $this->get(route('admin.verification.download_signature', $verification->id))
            ->assertRedirect(route('login'));
    }
}
