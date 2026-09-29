<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SavedListingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_rented_rooms_are_hidden_from_browse_and_saved_lists_without_deleting_saves(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $owner = User::factory()->create(['user_role' => 'student']);
        $room = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Saved room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active', 'listing_availability' => 'available']);
        $student->savedListings()->attach($room, ['save_created_at' => now()->subDays(2), 'save_updated_at' => now()->subDays(2)]);
        $this->actingAs($student)->get(route('student.saved'))->assertOk()->assertSee('Saved room');
        $this->get(route('student.listings'))->assertOk()->assertSee('Saved room');
        $room->update(['listing_availability' => 'rented']);

        $this->actingAs($student)->get(route('student.saved'))->assertOk()
            ->assertDontSee('Saved room')->assertViewHas('listings', fn ($listings) => $listings->total() === 0);
        $this->get(route('student.listings'))->assertOk()->assertDontSee('Saved room')
            ->assertViewHas('listings', fn ($listings) => $listings->total() === 0);
        $this->assertDatabaseHas('listing_saves', ['user_id' => $student->getKey(), 'listing_id' => $room->getKey()]);
        $room->update(['listing_availability' => 'available']);
        $this->get(route('student.saved'))->assertOk()->assertSee('Saved room');
        $this->get(route('student.listings'))->assertOk()->assertSee('Saved room');
        $room->update(['listing_availability' => 'rented']);
        $this->post(route('student.listings.save', $room))->assertRedirect();
        $this->assertDatabaseMissing('listing_saves', ['user_id' => $student->getKey(), 'listing_id' => $room->getKey()]);
        $this->get(route('student.saved'))->assertSee('No saved listings yet')->assertSee('Browse Room Listings');
        $this->postJson(route('student.listings.save', $room))->assertStatus(422);
    }

    public function test_dashboard_keeps_available_rooms_without_badges_and_excludes_rented_rooms(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        foreach (['available', 'rented'] as $availability) {
            Listing::create(['user_id' => $student->getKey(), 'listing_title' => 'Room '.$availability, 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active', 'listing_availability' => $availability]);
        }
        $this->actingAs($student)->get(route('student.dashboard'))->assertOk()
            ->assertSee('Room available')->assertDontSee('Room rented')
            ->assertDontSee('Available')->assertDontSee('Rented')
            ->assertViewHas('listings', fn ($listings) => $listings->count() === 1);
    }

    public function test_sorting_uses_saved_date_price_and_listing_date(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $owner = User::factory()->create(['user_role' => 'student']);
        foreach ([['Older cheap room', 400, 5, 1], ['Newer expensive room', 800, 1, 3]] as [$title, $rent, $age, $savedAge]) {
            $room = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => $title, 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => $rent, 'room_type' => 'Single', 'listing_status' => 'active', 'listing_availability' => 'available']);
            $room->forceFill(['listing_created_at' => now()->subDays($age)])->save();
            $student->savedListings()->attach($room, ['save_created_at' => now()->subDays($savedAge), 'save_updated_at' => now()]);
        }
        foreach (['recent', 'price_asc', 'invalid', 'price_desc', 'newest'] as $sort) {
            $expected = in_array($sort, ['price_desc', 'newest']) ? ['Newer expensive room', 'Older cheap room'] : ['Older cheap room', 'Newer expensive room'];
            $this->actingAs($student)->get(route('student.saved', ['sort' => $sort]))->assertOk()->assertSeeInOrder($expected);
        }
        $this->get(route('student.saved', ['page' => 2, 'sort' => 'price_asc']))
            ->assertRedirect(route('student.saved', ['sort' => 'price_asc', 'page' => 1]));
    }
}
