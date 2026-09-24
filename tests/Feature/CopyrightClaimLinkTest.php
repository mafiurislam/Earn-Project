<?php

namespace Tests\Feature;

use App\Models\CopyrightClaimLink;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CopyrightClaimLinkTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer1;

    protected User $customer2;

    protected function setUp(): void
    {
        parent::setUp();

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

        $this->customer1 = User::create([
            'name' => 'Simran Taufik',
            'username' => 'simrantaufik',
            'email' => 'simran@example.com',
            'phone' => '+91 9999900001',
            'password' => bcrypt('password'),
            'earning_balance' => 100.00,
            'total_earnings' => 500.00,
            'is_admin' => false,
        ]);

        $this->customer2 = User::create([
            'name' => 'Second Artist',
            'username' => 'secondartist',
            'email' => 'second@example.com',
            'phone' => '+91 9999900002',
            'password' => bcrypt('password'),
            'earning_balance' => 50.00,
            'total_earnings' => 200.00,
            'is_admin' => false,
        ]);
    }

    public function test_home_page_displays_copyright_claim_section_with_10_slots(): void
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Copyright Claim Remove Link');
        $response->assertSee('Verified submissions from our artists, listed in order from 1 to 10.');
        $response->assertSee('Slot available');
    }

    public function test_customer_can_upload_link_and_it_assigns_available_slot(): void
    {
        $response = $this->actingAs($this->customer1)
            ->post(route('customer.copyright_links.store'), [
                'title' => 'My Song Claim',
                'url' => 'https://app.base44.com/apps/preview',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('copyright_claim_links', [
            'user_id' => $this->customer1->id,
            'slot_number' => 1,
            'title' => 'My Song Claim',
            'url' => 'https://app.base44.com/apps/preview',
        ]);

        // Verify it reflects immediately on Home Page
        $homeResponse = $this->get(route('home'));
        $homeResponse->assertSee('My Song Claim');
        $homeResponse->assertSee('https://app.base44.com/apps/preview');
        $homeResponse->assertSee('by simrantaufik');
    }

    public function test_customer_can_update_own_link(): void
    {
        $link = CopyrightClaimLink::create([
            'user_id' => $this->customer1->id,
            'slot_number' => 1,
            'title' => 'Initial Title',
            'url' => 'https://example.com/original',
        ]);

        $response = $this->actingAs($this->customer1)
            ->put(route('customer.copyright_links.update', $link->id), [
                'title' => 'Updated Song Claim',
                'url' => 'https://example.com/updated',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('copyright_claim_links', [
            'id' => $link->id,
            'title' => 'Updated Song Claim',
            'url' => 'https://example.com/updated',
        ]);

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertSee('Updated Song Claim');
        $homeResponse->assertSee('https://example.com/updated');
    }

    public function test_customer_cannot_update_or_delete_another_customers_link(): void
    {
        $link = CopyrightClaimLink::create([
            'user_id' => $this->customer1->id,
            'slot_number' => 1,
            'title' => 'Customer 1 Track',
            'url' => 'https://example.com/track1',
        ]);

        // Customer 2 attempts to update Customer 1's link
        $updateResponse = $this->actingAs($this->customer2)
            ->put(route('customer.copyright_links.update', $link->id), [
                'title' => 'Hacked Title',
                'url' => 'https://example.com/hacked',
            ]);
        $updateResponse->assertStatus(404);

        // Customer 2 attempts to delete Customer 1's link
        $deleteResponse = $this->actingAs($this->customer2)
            ->delete(route('customer.copyright_links.destroy', $link->id));
        $deleteResponse->assertStatus(404);

        $this->assertDatabaseHas('copyright_claim_links', [
            'id' => $link->id,
            'title' => 'Customer 1 Track',
        ]);
    }

    public function test_customer_cannot_upload_more_than_10_links_system_wide(): void
    {
        // Occupy all 10 slots
        for ($i = 1; $i <= 10; $i++) {
            CopyrightClaimLink::create([
                'user_id' => $this->customer1->id,
                'slot_number' => $i,
                'title' => "Slot #{$i}",
                'url' => "https://example.com/slot/{$i}",
            ]);
        }

        // Attempt to upload 11th link
        $response = $this->actingAs($this->customer2)
            ->post(route('customer.copyright_links.store'), [
                'title' => 'Overflow Link',
                'url' => 'https://example.com/overflow',
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('copyright_claim_links', [
            'title' => 'Overflow Link',
        ]);
    }

    public function test_customer_can_delete_own_link_and_slot_becomes_available(): void
    {
        $link = CopyrightClaimLink::create([
            'user_id' => $this->customer1->id,
            'slot_number' => 1,
            'title' => 'To Delete',
            'url' => 'https://example.com/delete-me',
        ]);

        $response = $this->actingAs($this->customer1)
            ->delete(route('customer.copyright_links.destroy', $link->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('copyright_claim_links', ['id' => $link->id]);

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertDontSee('https://example.com/delete-me');
    }

    public function test_admin_can_view_all_links_in_admin_console(): void
    {
        CopyrightClaimLink::create([
            'user_id' => $this->customer1->id,
            'slot_number' => 1,
            'title' => 'Artist 1 Submission',
            'url' => 'https://example.com/artist1',
        ]);

        CopyrightClaimLink::create([
            'user_id' => $this->customer2->id,
            'slot_number' => 2,
            'title' => 'Artist 2 Submission',
            'url' => 'https://example.com/artist2',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Copyright claim remove links');
        $response->assertSee('Artist 1 Submission');
        $response->assertSee('Artist 2 Submission');
        $response->assertSee('Simran Taufik');
        $response->assertSee('Second Artist');
    }

    public function test_admin_can_update_link_and_swap_serial_slots(): void
    {
        $link1 = CopyrightClaimLink::create([
            'user_id' => $this->customer1->id,
            'slot_number' => 1,
            'title' => 'Track 1',
            'url' => 'https://example.com/track1',
        ]);

        $link2 = CopyrightClaimLink::create([
            'user_id' => $this->customer2->id,
            'slot_number' => 2,
            'title' => 'Track 2',
            'url' => 'https://example.com/track2',
        ]);

        // Admin reorders link1 to slot 2; link2 should be swapped to slot 1
        $response = $this->actingAs($this->admin)
            ->put(route('admin.copyright_links.update', $link1->id), [
                'title' => 'Track 1 Renamed',
                'url' => 'https://example.com/track1-new',
                'slot_number' => 2,
            ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals(2, $link1->fresh()->slot_number);
        $this->assertEquals('Track 1 Renamed', $link1->fresh()->title);
        $this->assertEquals(1, $link2->fresh()->slot_number);
    }

    public function test_admin_can_delete_any_link(): void
    {
        $link = CopyrightClaimLink::create([
            'user_id' => $this->customer1->id,
            'slot_number' => 5,
            'title' => 'Customer Song',
            'url' => 'https://example.com/song5',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.copyright_links.destroy', $link->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('copyright_claim_links', ['id' => $link->id]);
    }
}
