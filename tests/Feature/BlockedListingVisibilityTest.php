<?php
namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockedListingVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_listings_are_hidden_in_both_directions_and_return_after_unblocking(): void
    {
        $a = User::factory()->create(['user_role' => 'student']);
        $b = User::factory()->create(['user_role' => 'student']);
        $rooms = [];
        foreach ([$a, $b] as $owner) {
            $rooms[$owner->getKey()] = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Private room '.$owner->getKey(),
                'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single',
                'listing_status' => 'active', 'listing_availability' => 'available']);
        }
        $a->savedListings()->attach($rooms[$b->getKey()]);
        $b->savedListings()->attach($rooms[$a->getKey()]);
        $a->blockedUsers()->attach($b);
        foreach ([[$a, $b], [$b, $a]] as [$viewer, $owner]) {
            $room = $rooms[$owner->getKey()];
            $this->actingAs($viewer);
            foreach (['student.dashboard', 'student.listings', 'student.saved'] as $route) {
                $this->get(route($route))->assertOk()->assertDontSee($room->listing_title);
            }
            $this->get(route('student.listings.show', $room))->assertNotFound();
            $this->post(route('student.listings.save', $room))->assertNotFound();
            $this->get(route('student.messages', ['userId' => $owner->getKey(), 'listing_id' => $room->getKey()]))
                ->assertOk()->assertDontSee($room->listing_title);
            $this->get(route('student.listings.show', $rooms[$viewer->getKey()]))->assertOk();
        }
        $a->blockedUsers()->detach($b);
        $this->actingAs($a)->get(route('student.saved'))->assertOk()->assertSee($rooms[$b->getKey()]->listing_title);
        $this->get(route('student.listings.show', $rooms[$b->getKey()]))->assertOk();
    }
}
