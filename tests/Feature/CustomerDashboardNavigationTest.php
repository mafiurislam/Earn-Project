<?php

namespace Tests\Feature;

use App\Models\Song;
use App\Models\User;
use App\Models\Verification;
use App\Models\Withdrawal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerDashboardNavigationTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->customer = User::create([
            'name' => 'Simran Artist',
            'username' => 'simranartist',
            'email' => 'simran@example.com',
            'phone' => '+91 9999912345',
            'password' => bcrypt('password'),
            'earning_balance' => 1500.00,
            'total_earnings' => 3000.00,
            'withdrawn_amount' => 1500.00,
            'is_admin' => false,
        ]);

        Verification::create([
            'user_id' => $this->customer->id,
            'full_name' => 'Simran Artist',
            'pan_number' => 'ABCDE1234F',
            'pan_card_photo' => 'verifications/pan.jpg',
            'signature_photo' => 'verifications/sig.jpg',
            'bank_account' => '1234567890',
            'ifsc_code' => 'HDFC0001234',
            'phone' => '+91 9999912345',
            'email' => 'simran@example.com',
            'status' => 'approved',
        ]);
    }

    public function test_customer_dashboard_renders_without_direct_heavy_sections(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Welcome, <span style="color: #00d2aa;">simranartist</span>', false);
        $response->assertSee('TOTAL EARNINGS');
        $response->assertSee('AVAILABLE BALANCE');
        $response->assertSee('TOTAL WITHDRAWN');

        // Form actions for copyright links, songs, and withdrawal history table should NOT be on main dashboard
        $response->assertDontSee('name="composer"', false);
        $response->assertDontSee('name="singer"', false);
        $response->assertDontSee('Choose 3000 &times; 3000 image', false);
        $response->assertDontSee('Standard processing', false);

        // Quick access navigation links to dedicated pages should exist
        $response->assertSee(route('customer.copyright_links.index'));
        $response->assertSee(route('customer.songs.index'));
        $response->assertSee(route('customer.withdrawals.index'));
    }

    public function test_profile_icon_dropdown_contains_all_three_options(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);

        // Verify dropdown links
        $response->assertSee(route('customer.copyright_links.index'));
        $response->assertSee('Copyright Claim Remove Links');

        $response->assertSee(route('customer.songs.index'));
        $response->assertSee('Upload Song');

        $response->assertSee(route('customer.withdrawals.index'));
        $response->assertSee('Withdrawal History');
    }

    public function test_dedicated_copyright_links_page_renders_and_functions(): void
    {
        // 1. Visit dedicated page
        $response = $this->actingAs($this->customer)->get(route('customer.copyright_links.index'));
        $response->assertStatus(200);
        $response->assertSee('Copyright Claim Remove Links');
        $response->assertSee('Upload New Copyright Claim Link');

        // 2. Add a copyright link
        $postResponse = $this->actingAs($this->customer)
            ->post(route('customer.copyright_links.store'), [
                'title' => 'My Single Claim',
                'url' => 'https://youtube.com/watch?v=sample123',
            ]);
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('copyright_claim_links', [
            'user_id' => $this->customer->id,
            'title' => 'My Single Claim',
            'url' => 'https://youtube.com/watch?v=sample123',
        ]);

        // 3. Dedicated page now displays the added link
        $pageResponse = $this->actingAs($this->customer)->get(route('customer.copyright_links.index'));
        $pageResponse->assertSee('My Single Claim');
        $pageResponse->assertSee('https://youtube.com/watch?v=sample123');
    }

    public function test_dedicated_upload_song_page_renders_and_functions(): void
    {
        // 1. Visit dedicated page
        $response = $this->actingAs($this->customer)->get(route('customer.songs.index'));
        $response->assertStatus(200);
        $response->assertSee('Upload Song');
        $response->assertSee('Upload New Track');

        // 2. Upload song
        $cover = UploadedFile::fake()->image('album.jpg', 3000, 3000);
        $audio = UploadedFile::fake()->create('track.mp3', 2048, 'audio/mpeg');

        $postResponse = $this->actingAs($this->customer)
            ->post(route('customer.songs.store'), [
                'title' => 'Kesariya Ishq',
                'singer' => 'Arijit Singh',
                'composer' => 'Pritam',
                'producer' => 'Rajdoot Media',
                'cover_image' => $cover,
                'audio_file' => $audio,
            ]);
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('songs', [
            'user_id' => $this->customer->id,
            'title' => 'Kesariya Ishq',
            'singer' => 'Arijit Singh',
        ]);

        // 3. Dedicated page displays uploaded song
        $pageResponse = $this->actingAs($this->customer)->get(route('customer.songs.index'));
        $pageResponse->assertSee('Kesariya Ishq');
        $pageResponse->assertSee('Arijit Singh');
    }

    public function test_dedicated_withdrawals_page_renders_and_functions(): void
    {
        // 1. Create a withdrawal record
        Withdrawal::create([
            'user_id' => $this->customer->id,
            'amount' => 500.00,
            'status' => 'pending',
            'admin_note' => 'Under processing by finance',
        ]);

        // 2. Visit dedicated withdrawals page
        $response = $this->actingAs($this->customer)->get(route('customer.withdrawals.index'));
        $response->assertStatus(200);
        $response->assertSee('Withdrawal History &amp; Payouts', false);
        $response->assertSee('₹1,500.00');
        $response->assertSee('₹500.00');
        $response->assertSee('Under processing by finance');

        // 3. Request a new withdrawal from the dedicated page
        $postResponse = $this->actingAs($this->customer)
            ->post(route('customer.withdrawal.request'), [
                'amount' => 200.00,
            ]);
        $postResponse->assertSessionHas('success');

        $this->assertDatabaseHas('withdrawals', [
            'user_id' => $this->customer->id,
            'amount' => 200.00,
            'status' => 'pending',
        ]);
    }
}
