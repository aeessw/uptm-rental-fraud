<?php
namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuspendedListingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsuspension_restores_only_listings_hidden_by_suspension(): void
    {
        $admin = User::factory()->create(['user_role' => 'mpp']);
        $owner = User::factory()->create(['user_role' => 'student']);
        $viewer = User::factory()->create(['user_role' => 'student']);
        $data = ['user_id' => $owner->getKey(), 'listing_title' => 'Room', 'listing_description' => 'Room',
            'listing_location' => 'Cheras', 'listing_rent' => 400, 'room_type' => 'Single Room'];
        $active = Listing::create($data + ['listing_status' => 'active', 'review_status' => 'approved']);
        $hidden = Listing::create($data + ['listing_status' => 'hidden']);
        $removed = Listing::create($data + ['listing_status' => 'active', 'review_status' => 'approved']);
        $this->actingAs($admin)->post(route('mpp.users.suspend', $owner->getKey()), [
            'reason' => 'Fraud-related activity',
        ])->assertRedirect();
        $this->assertSame('hidden', $active->fresh()->listing_status);
        $this->assertFalse(Listing::visibleTo($viewer)->whereKey($active->getKey())->exists());
        $this->post(route('mpp.listings.remove', $removed->getKey()), ['reason' => 'Fraud / Scam'])->assertRedirect();
        $this->post(route('mpp.users.unsuspend', $owner->getKey()))->assertRedirect();
        $this->assertSame('active', $active->fresh()->listing_status);
        $this->assertTrue(Listing::visibleTo($viewer)->whereKey($active->getKey())->exists());
        $this->assertSame('hidden', $hidden->fresh()->listing_status);
        $this->assertSame('hidden', $removed->fresh()->listing_status);
    }
}
