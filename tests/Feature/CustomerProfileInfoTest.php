<?php

namespace Tests\Feature;

use App\Models\CustomerProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerProfileInfoTest extends TestCase
{
    use RefreshDatabase;

    protected User $customer;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customer = User::create([
            'name' => 'Mafiur Islam',
            'username' => 'mafiurislam',
            'email' => 'mafiurislam@example.com',
            'password' => bcrypt('password'),
            'earning_balance' => 2500.00,
            'total_earnings' => 5000.00,
            'is_admin' => false,
        ]);

        $this->admin = User::create([
            'name' => 'Admin Owner',
            'username' => 'adminowner',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);
    }

    public function test_dropdown_contains_profile_info_with_red_dot_when_incomplete(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Profile Info');
        $response->assertSee('profile-status-dot dot-red', false);
        $response->assertSee('customerProfileInfoModal');
        $response->assertSee('name="owner_name"', false);
        $response->assertSee('name="channel_name"', false);
        $response->assertSee('name="youtube_link"', false);
        $response->assertSee('name="label_name"', false);
    }

    public function test_customer_cannot_submit_profile_info_with_missing_fields(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.profile_info.store'), [
                'owner_name' => '',
                'channel_name' => '',
                'youtube_link' => '',
                'label_name' => '',
            ]);

        $response->assertSessionHasErrors([
            'owner_name',
            'channel_name',
            'youtube_link',
            'label_name',
        ]);

        $this->assertDatabaseCount('customer_profiles', 0);
        $this->assertFalse($this->customer->fresh()->isProfileComplete());
    }

    public function test_customer_cannot_submit_if_any_single_field_is_missing(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.profile_info.store'), [
                'owner_name' => 'Mafiur Islam',
                'channel_name' => 'Mafiur Music',
                'youtube_link' => 'https://youtube.com/@mafiur',
                'label_name' => '', // missing label name
            ]);

        $response->assertSessionHasErrors(['label_name']);
        $this->assertDatabaseCount('customer_profiles', 0);
    }

    public function test_customer_can_successfully_submit_all_four_fields_via_standard_post(): void
    {
        $response = $this->actingAs($this->customer)
            ->post(route('customer.profile_info.store'), [
                'owner_name' => 'Mafiur Islam',
                'channel_name' => 'Official Mafiur Channel',
                'youtube_link' => 'https://youtube.com/@mafiurislam',
                'label_name' => 'Mafiur Records',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('customer_profiles', [
            'user_id' => $this->customer->id,
            'owner_name' => 'Mafiur Islam',
            'channel_name' => 'Official Mafiur Channel',
            'youtube_link' => 'https://youtube.com/@mafiurislam',
            'label_name' => 'Mafiur Records',
        ]);

        $this->assertTrue($this->customer->fresh()->isProfileComplete());
    }

    public function test_customer_can_submit_via_ajax_and_receive_json(): void
    {
        $response = $this->actingAs($this->customer)
            ->postJson(route('customer.profile_info.store'), [
                'owner_name' => 'Simran Taufik',
                'channel_name' => 'Simran Beats',
                'youtube_link' => 'https://youtube.com/@simranbeats',
                'label_name' => 'Simran Audio Label',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'is_complete' => true,
            'data' => [
                'owner_name' => 'Simran Taufik',
                'channel_name' => 'Simran Beats',
                'youtube_link' => 'https://youtube.com/@simranbeats',
                'label_name' => 'Simran Audio Label',
            ],
        ]);

        $this->assertTrue($this->customer->fresh()->isProfileComplete());
    }

    public function test_dropdown_shows_green_dot_when_profile_is_complete(): void
    {
        CustomerProfile::create([
            'user_id' => $this->customer->id,
            'owner_name' => 'Mafiur Islam',
            'channel_name' => 'Mafiur Channel',
            'youtube_link' => 'https://youtube.com/@mafiur',
            'label_name' => 'Mafiur Media',
        ]);

        $response = $this->actingAs($this->customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('profile-status-dot dot-green', false);
        $response->assertDontSee('profile-status-dot dot-red', false);
        $response->assertSee('Completed');
    }

    public function test_customer_can_update_profile_info_without_creating_duplicate_records(): void
    {
        CustomerProfile::create([
            'user_id' => $this->customer->id,
            'owner_name' => 'Original Name',
            'channel_name' => 'Original Channel',
            'youtube_link' => 'https://youtube.com/@orig',
            'label_name' => 'Original Label',
        ]);

        $this->assertDatabaseCount('customer_profiles', 1);

        $response = $this->actingAs($this->customer)
            ->post(route('customer.profile_info.store'), [
                'owner_name' => 'Updated Name',
                'channel_name' => 'Updated Channel',
                'youtube_link' => 'https://youtube.com/@updated',
                'label_name' => 'Updated Label',
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseCount('customer_profiles', 1);
        $this->assertDatabaseHas('customer_profiles', [
            'user_id' => $this->customer->id,
            'owner_name' => 'Updated Name',
            'channel_name' => 'Updated Channel',
            'youtube_link' => 'https://youtube.com/@updated',
            'label_name' => 'Updated Label',
        ]);
    }

    public function test_admin_dashboard_displays_view_profile_info_button_and_isolated_modal(): void
    {
        CustomerProfile::create([
            'user_id' => $this->customer->id,
            'owner_name' => 'Mafiur Islam',
            'channel_name' => 'Mafiur Channel',
            'youtube_link' => 'https://youtube.com/@mafiur',
            'label_name' => 'Mafiur Records',
        ]);

        $customer2 = User::create([
            'name' => 'Second Customer',
            'username' => 'secondcust',
            'email' => 'second@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);

        CustomerProfile::create([
            'user_id' => $customer2->id,
            'owner_name' => 'Second Owner',
            'channel_name' => 'Second Channel',
            'youtube_link' => 'https://youtube.com/@second',
            'label_name' => 'Second Music Label',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('View Profile Info');
        $response->assertSee('viewProfileInfoModal'.$this->customer->id);
        $response->assertSee('viewProfileInfoModal'.$customer2->id);

        // Check specific details exist
        $response->assertSee('Mafiur Channel');
        $response->assertSee('Mafiur Records');
        $response->assertSee('Second Channel');
        $response->assertSee('Second Music Label');
    }

    public function test_admin_customer_show_page_displays_profile_info_section(): void
    {
        CustomerProfile::create([
            'user_id' => $this->customer->id,
            'owner_name' => 'Mafiur Islam',
            'channel_name' => 'Mafiur Channel',
            'youtube_link' => 'https://youtube.com/@mafiur',
            'label_name' => 'Mafiur Records',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.customers.show', $this->customer->id));

        $response->assertSee('Customer Profile Information');
        $response->assertSee('Mafiur Islam');
        $response->assertSee('Mafiur Channel');
        $response->assertSee('https://youtube.com/@mafiur');
        $response->assertSee('Mafiur Records');
    }

    public function test_dropdown_contains_upload_autocart_generator_with_toggle_switch(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Upload Autocart Generator');
        $response->assertSee('autocartToggleInput');
        $response->assertSee('custom-switch-toggle', false);
    }

    public function test_customer_can_toggle_autocart_generator_on_and_off(): void
    {
        // 1. Turn ON
        $responseOn = $this->actingAs($this->customer)
            ->postJson(route('customer.autocart_generator.toggle'), [
                'enabled' => true,
            ]);

        $responseOn->assertStatus(200);
        $responseOn->assertJson([
            'success' => true,
            'enabled' => true,
            'status_text' => 'ON',
        ]);
        $this->assertTrue($this->customer->fresh()->autocart_generator_enabled);

        // 2. Turn OFF
        $responseOff = $this->actingAs($this->customer)
            ->postJson(route('customer.autocart_generator.toggle'), [
                'enabled' => false,
            ]);

        $responseOff->assertStatus(200);
        $responseOff->assertJson([
            'success' => true,
            'enabled' => false,
            'status_text' => 'OFF',
        ]);
        $this->assertFalse($this->customer->fresh()->autocart_generator_enabled);
    }

    public function test_admin_dashboard_displays_autocart_generator_is_on_in_green_when_enabled(): void
    {
        $this->customer->update(['autocart_generator_enabled' => true]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Autocart Generator is ON');
        $response->assertSee('text-success', false);
        $response->assertSee('color: #10b981', false);
    }

    public function test_admin_dashboard_displays_autocart_generator_is_off_in_red_when_disabled(): void
    {
        $this->customer->update(['autocart_generator_enabled' => false]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Autocart Generator is OFF');
        $response->assertSee('text-danger', false);
        $response->assertSee('color: #ef4444', false);
    }
}
