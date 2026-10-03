<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
class MppListingReviewTest extends TestCase
{
    use RefreshDatabase;
    public function test_mpp_can_inspect_filter_hide_and_restore(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $rooms = collect(['Review room', 'Other room'])->map(fn ($title) => Listing::create(['user_id' => $owner->getKey(), 'listing_title' => $title, 'listing_description' => 'Review description', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active', 'review_status' => 'approved', 'listing_availability' => 'rented', 'report_count' => 2]));
        $room = $rooms->first();
        $this->actingAs(User::factory()->create(['user_role' => 'mpp']))->get(route('mpp.listings'))->assertOk()->assertSee('Availability')->assertSee('Review description')->assertSee('listing-details-'.$room->getKey())->assertSee('Hide Listing');
        $this->get(route('mpp.reports', ['listing_id' => $room->getKey()]))->assertOk()->assertViewHas('reportedListings', fn ($list) => $list->isEmpty());
        $this->post(route('mpp.listings.remove', $room))->assertSessionHasErrors('reason');
        $this->assertSame('active', $room->fresh()->listing_status);
        $this->post(route('mpp.listings.remove', $room), ['reason' => 'Fraud reports'])->assertRedirect();
        $this->assertSame('hidden', $room->fresh()->listing_status);
        $this->assertSame('rented', $room->fresh()->listing_availability);
        $this->get(route('mpp.listings'))->assertOk()->assertSee('Restore Listing');
        $this->post(route('mpp.listings.restore', $room))->assertRedirect();
        $this->assertSame('active', $room->fresh()->listing_status);
        $this->actingAs($owner)->post(route('mpp.listings.remove', $room))->assertRedirect('/');
        $this->assertSame('active', $room->fresh()->listing_status);
    }

    public function test_rejection_hides_and_approval_publishes(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $room = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Review room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active', 'review_status' => 'approved']);
        $this->actingAs($owner)->post(route('mpp.listings.reject', $room), ['reason' => 'Other', 'note' => 'Invalid'])->assertRedirect('/');
        $this->actingAs(User::factory()->create(['user_role' => 'mpp']));
        $this->get(route('mpp.listings'))->assertOk()->assertSeeInOrder(['Availability', 'Reports', 'Review Status', 'Status', 'Action']);
        $this->post(route('mpp.listings.reject', $room))->assertSessionHasErrors('reason');
        $this->post(route('mpp.listings.reject', $room), ['reason' => 'Misleading information'])->assertRedirect();
        $this->assertSame('rejected', $room->fresh()->review_status);
        $this->assertSame('hidden', $room->fresh()->listing_status);
        $this->post(route('mpp.listings.restore', $room))->assertStatus(409);
        $this->post(route('mpp.listings.approve', $room))->assertRedirect();
        $this->assertSame('approved', $room->fresh()->review_status);
        $this->assertSame('active', $room->fresh()->listing_status);
        $this->assertSame('active', $room->fresh()->listing_status);
        $this->post(route('mpp.listings.remove', $room), ['reason' => 'Fraud reports'])->assertRedirect();
        $this->assertSame('approved', $room->fresh()->review_status);
    }
}
