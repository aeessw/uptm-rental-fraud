<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ListingDetailsTest extends TestCase
{
    use RefreshDatabase;

    private function payload(): array
    {
        return [
            'title' => 'Student room', 'description' => 'A quiet room',
            'location' => 'Cheras', 'rent' => 450, 'room_type' => 'Single',
            'available_from' => '2026-10-01', 'rental_period' => 'flexible',
            'preferred_tenant' => 'any', 'facilities' => ['wifi', 'fridge'],
            'accuracy_confirmed' => '1', 'pax' => 2,
            'photos' => array_map(fn ($i) => UploadedFile::fake()->createWithContent("room{$i}.png", base64_decode("iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+aD1sAAAAASUVORK5CYII=")), range(1, 3)),
        ];
    }

    public function test_post_edit_and_display_listing_details(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['user_role' => 'student']);
        $this->actingAs($owner)->get(route('student.listings.create'))->assertOk()->assertSee('accuracy_confirmed');
        $this->post(route('student.listings.store'), $this->payload())->assertSessionHasNoErrors()->assertRedirect();
        $listing = Listing::firstOrFail();
        $this->assertSame(2, $listing->pax);
        $listing->update(['listing_status' => 'active', 'review_status' => 'approved']);
        $this->get('/student/listings')->assertOk()->assertSee('2 people');
        $this->assertSame('available', $listing->listing_availability);
        $this->assertSame(['wifi', 'fridge'], $listing->facilities);
        $this->assertSame('2026-10-01', $listing->available_from->format('Y-m-d'));
        $this->get(route('student.listings.show', $listing))->assertOk()->assertSee('01 Oct 2026')->assertSee('WiFi')->assertSee('Flexible')->assertSee('2 people');
        $this->get(route('student.listings.edit', $listing))->assertOk()->assertSee('2026-10-01');
        $data = $this->payload();
        unset($data['photos'], $data['accuracy_confirmed']);
        $data['availability'] = 'rented';
        $data['pax'] = 1;
        $data['available_from'] = '2026-11-01';
        $data['facilities'] = ['wifi'];
        $this->put(route('student.listings.update', $listing), $data)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame(1, $listing->fresh()->pax);
        $this->get(route('student.listings.show', $listing))->assertOk()->assertSee('1 person');
        $this->assertSame(['wifi'], $listing->fresh()->facilities);
        $this->assertSame('2026-11-01', $listing->fresh()->available_from->format('Y-m-d'));
        $this->assertSame('rented', $listing->fresh()->listing_availability);
    }

    public function test_confirmation_and_option_validation_are_enforced_on_server(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['user_role' => 'student']));
        $data = $this->payload();
        unset($data['accuracy_confirmed']);
        $data['available_from'] = 'not-a-date';
        $data['rental_period'] = 'invalid';
        $data['preferred_tenant'] = 'invalid';
        $data['facilities'] = ['invented'];
        $data['pax'] = 0;
        $this->post(route('student.listings.store'), $data)->assertSessionHasErrors(['accuracy_confirmed', 'available_from', 'rental_period', 'preferred_tenant', 'facilities.0', 'pax']);
        $this->assertDatabaseCount('listings', 0);
    }

    public function test_all_room_preferences_are_required_on_create_and_update(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['user_role' => 'student']);
        $this->actingAs($owner);
        $data = $this->payload();
        $fields = ['pax', 'available_from', 'rental_period', 'preferred_tenant', 'facilities'];
        foreach ($fields as $field) {
            unset($data[$field]);
        }
        $this->post(route('student.listings.store'), $data)->assertSessionHasErrors($fields);
        $this->assertDatabaseCount('listings', 0);
        $listing = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Legacy room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active', 'review_status' => 'approved']);
        unset($data['photos']);
        $data['availability'] = 'available';
        $this->put(route('student.listings.update', $listing), $data)->assertSessionHasErrors($fields);
        $this->assertSame('Legacy room', $listing->fresh()->listing_title);
    }

    public function test_listings_can_be_filtered_by_pax(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $this->actingAs($owner);
        foreach ([null, 1, 2, 5, 6] as $pax) {
            Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Capacity '.($pax ?? 'unknown'), 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Shared', 'listing_status' => 'active', 'review_status' => 'approved', 'pax' => $pax]);
        }
        $this->get('/student/listings?pax=2')->assertOk()->assertSee('Capacity 2')->assertDontSee('Capacity 1')->assertDontSee('Capacity unknown')->assertDontSee('Capacity 5');
        $this->get('/student/listings?pax=5%2B')->assertOk()->assertSee('Capacity 5')->assertSee('Capacity 6')->assertDontSee('Capacity 2');
        $this->get('/student/listings?pax=')->assertOk()->assertSee('Capacity unknown')->assertSee('Capacity 1')->assertSee('Capacity 6');
        $this->getJson('/student/listings?pax=invalid')->assertUnprocessable()->assertJsonValidationErrors('pax');
    }

    public function test_legacy_listing_renders_and_other_students_cannot_edit_it(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $listing = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Legacy room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active', 'review_status' => 'approved']);
        $this->actingAs(User::factory()->create(['user_role' => 'student']));
        $this->get(route('student.listings.show', $listing))->assertOk()->assertSee('Facilities not specified.')->assertSee('Pax not specified');
        $this->put(route('student.listings.update', $listing), [])->assertForbidden();
    }

    public function test_two_post_limit_and_required_mpp_approval(): void
    {
        Storage::fake('public');
        $owner = User::factory()->create(['user_role' => 'student']);
        $viewer = User::factory()->create(['user_role' => 'student']);
        $mpp = User::factory()->create(['user_role' => 'mpp']);
        $this->actingAs($owner);
        for ($i = 0; $i < 2; $i++) {
            $this->post(route('student.listings.store'), $this->payload())
                ->assertSessionHasNoErrors()->assertRedirect();
        }
        $listing = Listing::firstOrFail();
        $this->assertSame('hidden', $listing->listing_status);
        $this->assertSame('pending', $listing->review_status);
        $this->get(route('student.listings.show', $listing))->assertOk();
        $this->get(route('student.listings.create'))->assertRedirect(route('student.profile'));
        $this->post(route('student.listings.store'), $this->payload())->assertSessionHasErrors('listing_limit');
        $this->assertDatabaseCount('listings', 2);
        $this->assertDatabaseCount('listing_photos', 6);
        $this->actingAs($viewer)->get(route('student.listings'))->assertDontSee('Student room');
        $this->get(route('student.dashboard'))->assertDontSee('Student room');
        $this->get(route('student.listings.show', $listing))->assertNotFound();
        $this->post(route('student.listings.save', $listing))->assertNotFound();
        $this->post(route('mpp.listings.approve', $listing))->assertRedirect('/');
        $this->assertSame('hidden', $listing->fresh()->listing_status);
        $this->assertSame('pending', $listing->fresh()->review_status);
        $this->actingAs($mpp)->get(route('mpp.listings'))->assertOk()->assertSee('Approve Listing');
        $this->post(route('mpp.listings.restore', $listing))->assertStatus(409);
        $this->post(route('mpp.listings.approve', $listing))->assertRedirect();
        $this->assertSame('active', $listing->fresh()->listing_status);
        $this->assertSame('approved', $listing->fresh()->review_status);
        $this->actingAs($viewer)->get(route('student.listings'))->assertSee('Student room');
        $this->get(route('student.listings.show', $listing))->assertOk();
        $data = $this->payload();
        unset($data['photos']);
        $data['availability'] = 'available';
        $this->actingAs($owner)->put(route('student.listings.update', $listing), $data)->assertSessionHasNoErrors();
        $this->assertSame('hidden', $listing->fresh()->listing_status);
        $this->assertSame('pending', $listing->fresh()->review_status);
        $this->actingAs($viewer)->get(route('student.listings.show', $listing))->assertNotFound();
    }

    public function test_hidden_posts_still_count_toward_limit(): void
    {
        Storage::fake('public');
        $this->actingAs(User::factory()->create(['user_role' => 'student']));
        for ($i = 0; $i < 2; $i++) {
            $this->post(route('student.listings.store'), $this->payload())->assertSessionHasNoErrors();
        }
        Listing::query()->update(['listing_status' => 'hidden']);
        $this->post(route('student.listings.store'), $this->payload())->assertSessionHasErrors('listing_limit');
        $this->assertDatabaseCount('listings', 2);
    }
}
