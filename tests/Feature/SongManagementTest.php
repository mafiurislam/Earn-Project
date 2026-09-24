<?php

namespace Tests\Feature;

use App\Models\Song;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SongManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::create([
            'name' => 'Admin Owner',
            'username' => 'adminowner',
            'email' => 'admin@example.com',
            'phone' => '+91 9876543210',
            'password' => bcrypt('password'),
            'earning_balance' => 0.00,
            'total_earnings' => 0.00,
            'is_admin' => true,
        ]);

        $this->customer = User::create([
            'name' => 'Arijit Music',
            'username' => 'arijitmusic',
            'email' => 'arijit@example.com',
            'phone' => '+91 9999900001',
            'password' => bcrypt('password'),
            'earning_balance' => 2500.00,
            'total_earnings' => 5000.00,
            'is_admin' => false,
        ]);
    }

    public function test_customer_can_upload_song_with_all_required_details(): void
    {
        $coverFile = UploadedFile::fake()->image('cover.jpg', 3000, 3000);
        $audioFile = UploadedFile::fake()->create('track.mp3', 3000, 'audio/mpeg');

        $response = $this->actingAs($this->customer)
            ->post(route('customer.songs.store'), [
                'title' => 'Chaleya Romance',
                'singer' => 'Arijit Singh, Shilpa Rao',
                'composer' => 'Anirudh Ravichander',
                'producer' => 'Rajdoot Media Records',
                'cover_image' => $coverFile,
                'audio_file' => $audioFile,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('songs', [
            'user_id' => $this->customer->id,
            'title' => 'Chaleya Romance',
            'singer' => 'Arijit Singh, Shilpa Rao',
            'composer' => 'Anirudh Ravichander',
            'producer' => 'Rajdoot Media Records',
            'copyright' => '℗ 2026 Rajdoot Nivedan',
            'status' => 'active',
        ]);

        $song = Song::where('title', 'Chaleya Romance')->first();
        $this->assertNotNull($song);
        $this->assertTrue(Storage::disk('public')->exists($song->cover_image));
        $this->assertTrue(Storage::disk('public')->exists($song->audio_file));
    }

    public function test_song_upload_validates_required_fields(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.songs.store'), []);

        $response->assertSessionHasErrors([
            'title',
            'singer',
            'composer',
            'producer',
            'cover_image',
            'audio_file',
        ]);
    }

    public function test_customer_can_update_their_song(): void
    {
        $coverFile = UploadedFile::fake()->image('cover.jpg', 3000, 3000);
        $audioFile = UploadedFile::fake()->create('track.mp3', 3000, 'audio/mpeg');

        $this->actingAs($this->customer)
            ->post(route('customer.songs.store'), [
                'title' => 'Initial Title',
                'singer' => 'Singer One',
                'composer' => 'Composer One',
                'producer' => 'Producer One',
                'cover_image' => $coverFile,
                'audio_file' => $audioFile,
            ]);

        $song = Song::first();

        $response = $this->actingAs($this->customer)
            ->put(route('customer.songs.update', $song->id), [
                'title' => 'Updated Title',
                'singer' => 'Updated Singer',
                'composer' => 'Updated Composer',
                'producer' => 'Updated Producer',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'title' => 'Updated Title',
            'singer' => 'Updated Singer',
        ]);
    }

    public function test_customer_can_delete_their_song(): void
    {
        $coverFile = UploadedFile::fake()->image('cover.jpg', 3000, 3000);
        $audioFile = UploadedFile::fake()->create('track.mp3', 3000, 'audio/mpeg');

        $this->actingAs($this->customer)
            ->post(route('customer.songs.store'), [
                'title' => 'Song To Delete',
                'singer' => 'Singer X',
                'composer' => 'Composer X',
                'producer' => 'Producer X',
                'cover_image' => $coverFile,
                'audio_file' => $audioFile,
            ]);

        $song = Song::first();
        $coverPath = $song->cover_image;
        $audioPath = $song->audio_file;

        $response = $this->actingAs($this->customer)
            ->delete(route('customer.songs.destroy', $song->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('songs', ['id' => $song->id]);
        $this->assertFalse(Storage::disk('public')->exists($coverPath));
        $this->assertFalse(Storage::disk('public')->exists($audioPath));
    }

    public function test_admin_can_view_individual_customer_profile(): void
    {
        // Upload a song for the customer first
        $coverFile = UploadedFile::fake()->image('cover.jpg', 3000, 3000);
        $audioFile = UploadedFile::fake()->create('track.mp3', 3000, 'audio/mpeg');

        $this->actingAs($this->customer)
            ->post(route('customer.songs.store'), [
                'title' => 'Profile Track One',
                'singer' => 'Singer Y',
                'composer' => 'Composer Y',
                'producer' => 'Producer Y',
                'cover_image' => $coverFile,
                'audio_file' => $audioFile,
            ]);

        $response = $this->actingAs($this->admin)
            ->get(route('admin.customers.show', $this->customer->id));

        $response->assertOk();
        $response->assertSee('Arijit Music');
        $response->assertSee('Profile Track One');
        $response->assertSee('Singer Y');
        $response->assertSee('Composer Y');
        $response->assertSee('Producer Y');
    }

    public function test_admin_can_create_new_customer(): void
    {
        $response = $this->actingAs($this->admin)
            ->post(route('admin.customers.store'), [
                'name' => 'New Customer Alpha',
                'username' => 'newalpha',
                'email' => 'alpha@example.com',
                'phone' => '+91 9123456780',
                'password' => 'secret123',
                'earning_balance' => 500.00,
            ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New Customer Alpha',
            'email' => 'alpha@example.com',
            'username' => 'newalpha',
        ]);

        $newCustomer = User::where('email', 'alpha@example.com')->first();
        $response->assertRedirect(route('admin.customers.show', $newCustomer->id));
    }

    public function test_admin_can_edit_customer_details(): void
    {
        $response = $this->actingAs($this->admin)
            ->put(route('admin.customers.update', $this->customer->id), [
                'name' => 'Arijit Music Updated',
                'username' => 'arijitmusicupdated',
                'email' => 'arijit_updated@example.com',
                'phone' => '+91 9999900099',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'id' => $this->customer->id,
            'name' => 'Arijit Music Updated',
            'email' => 'arijit_updated@example.com',
        ]);
    }

    public function test_admin_can_upload_song_for_customer(): void
    {
        $coverFile = UploadedFile::fake()->image('cover.jpg', 3000, 3000);
        $audioFile = UploadedFile::fake()->create('track.mp3', 3000, 'audio/mpeg');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.customers.songs.store', $this->customer->id), [
                'title' => 'Admin Uploaded Track',
                'singer' => 'Admin Chosen Singer',
                'composer' => 'Admin Composer',
                'producer' => 'Admin Producer',
                'cover_image' => $coverFile,
                'audio_file' => $audioFile,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('songs', [
            'user_id' => $this->customer->id,
            'title' => 'Admin Uploaded Track',
            'singer' => 'Admin Chosen Singer',
            'copyright' => '℗ 2026 Rajdoot Nivedan',
        ]);
    }

    public function test_admin_can_update_song(): void
    {
        $song = Song::create([
            'user_id' => $this->customer->id,
            'title' => 'Before Admin Edit',
            'singer' => 'Singer 1',
            'composer' => 'Composer 1',
            'producer' => 'Producer 1',
            'cover_image' => 'songs/covers/test.jpg',
            'audio_file' => 'songs/audio/test.mp3',
        ]);

        $response = $this->actingAs($this->admin)
            ->put(route('admin.songs.update', $song->id), [
                'title' => 'After Admin Edit',
                'singer' => 'New Singer',
                'composer' => 'New Composer',
                'producer' => 'New Producer',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('songs', [
            'id' => $song->id,
            'title' => 'After Admin Edit',
            'singer' => 'New Singer',
        ]);
    }

    public function test_admin_can_delete_song(): void
    {
        $song = Song::create([
            'user_id' => $this->customer->id,
            'title' => 'Song To Delete By Admin',
            'singer' => 'Singer',
            'composer' => 'Composer',
            'producer' => 'Producer',
            'cover_image' => 'songs/covers/test.jpg',
            'audio_file' => 'songs/audio/test.mp3',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.songs.destroy', $song->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('songs', ['id' => $song->id]);
    }

    public function test_newly_uploaded_track_automatically_includes_copyright_text(): void
    {
        $coverFile = UploadedFile::fake()->image('cover.jpg', 3000, 3000);
        $audioFile = UploadedFile::fake()->create('track.mp3', 3000, 'audio/mpeg');

        // Customer uploads a song without specifying copyright
        $response = $this->actingAs($this->customer)
            ->post(route('customer.songs.store'), [
                'title' => 'Auto Copyright Song',
                'singer' => 'Singer Z',
                'composer' => 'Composer Z',
                'producer' => 'Producer Z',
                'cover_image' => $coverFile,
                'audio_file' => $audioFile,
            ]);

        $response->assertSessionHas('success');

        // Song must have copyright automatically set to "℗ 2026 Rajdoot Nivedan"
        $this->assertDatabaseHas('songs', [
            'title' => 'Auto Copyright Song',
            'copyright' => '℗ 2026 Rajdoot Nivedan',
        ]);

        $song = Song::where('title', 'Auto Copyright Song')->first();
        $this->assertSame('℗ 2026 Rajdoot Nivedan', $song->copyright);

        // Verify that the customer song catalog view displays this copyright text
        $customerViewResponse = $this->actingAs($this->customer)->get(route('customer.songs.index'));
        $customerViewResponse->assertOk();
        $customerViewResponse->assertSee('℗ 2026 Rajdoot Nivedan');

        // Verify that the admin customer show view displays this copyright text
        $adminViewResponse = $this->actingAs($this->admin)->get(route('admin.customers.show', $this->customer->id));
        $adminViewResponse->assertOk();
        $adminViewResponse->assertSee('℗ 2026 Rajdoot Nivedan');
    }

    public function test_admin_can_delete_customer_profile(): void
    {
        $response = $this->actingAs($this->admin)
            ->delete(route('admin.customers.destroy', $this->customer->id));

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseMissing('users', ['id' => $this->customer->id]);
    }
}
