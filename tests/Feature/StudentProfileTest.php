<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_shows_only_owned_listings_and_edit_links(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student']);
        $attributes = ['listing_description' => 'Room description', 'listing_location' => 'Cheras', 'listing_rent' => 500, 'room_type' => 'Single'];
        $owned = Listing::create($attributes + ['user_id' => $owner->getKey(), 'listing_title' => 'My own room', 'listing_status' => 'removed']);
        Listing::create($attributes + ['user_id' => $other->getKey(), 'listing_title' => 'Someone else room']);

        $this->actingAs($owner)->get(route('student.profile'))
            ->assertOk()
            ->assertSee('My own room')
            ->assertSee('Removed')
            ->assertSee(route('student.listings.edit', $owned), false)
            ->assertDontSee('Someone else room');

        $this->actingAs($other)->get(route('student.listings.edit', $owned))->assertForbidden();
    }

    public function test_empty_profile_and_guest_redirect(): void
    {
        $this->get(route('student.profile'))->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create(['user_role' => 'student']))
            ->get(route('student.profile'))
            ->assertOk()
            ->assertSee('No listings yet');
    }
}