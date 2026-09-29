<?php

namespace Tests\Feature;

use App\Helpers\EncryptionHelper;
use App\Models\{Listing, Message, User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RenamedColumnFlowsTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_availability_saves_and_notification_payloads_keep_their_existing_interface(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $viewer = User::factory()->create(['user_role' => 'student']);
        $room = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Matching room', 'listing_description' => 'Quiet', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single']);
        Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Other room', 'listing_description' => 'Other', 'listing_location' => 'Ampang', 'listing_rent' => 1200, 'room_type' => 'Shared']);
        $this->actingAs($viewer)->get(route('student.listings', ['search' => 'Matching', 'location' => 'Cheras', 'rent' => 'below500', 'room_type' => 'Single', 'availability' => 'available', 'sort' => 'price_asc']))->assertOk()->assertSee('Matching room')->assertDontSee('Other room');
        $this->get(route('student.listings', ['sort' => 'price_desc']))->assertOk()->assertSeeInOrder(['Other room', 'Matching room']);
        $this->get(route('student.listings.show', $room))->assertOk();
        $this->getJson(route('student.messages.unread-count'))->assertOk()->assertJsonFragment(['id' => $room->getKey(), 'title' => 'Matching room']);
        $this->post(route('student.listings.save', $room))->assertRedirect();
        $this->post(route('student.saved.bulk-remove'), ['listing_ids' => [$room->getKey()]])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('listing_saves', 0);
        $this->actingAs($owner)->patch(route('student.listings.availability', $room), ['availability' => 'rented'])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('rented', $room->fresh()->listing_availability);
    }

    public function test_session_authentication_and_pivot_timestamps_use_the_custom_columns(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $viewer = User::factory()->create(['user_role' => 'student']);
        Auth::login($viewer);
        Auth::forgetGuards();
        $this->assertSame($viewer->getKey(), Auth::id());
        $this->assertSame($viewer->user_email, $viewer->getEmailForPasswordReset());
        $this->assertSame($viewer->user_email, $viewer->getEmailForVerification());
        $viewer->blockedUsers()->attach($owner);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $viewer->blockedUsers->sole()->pivot->block_created_at);
        $viewer->blockedUsers()->detach($owner);
        Message::create(['sender_id' => $owner->getKey(), 'receiver_id' => $viewer->getKey(), 'message_content' => EncryptionHelper::encrypt('Hello')]);
        $this->actingAs($viewer)->postJson(route('student.messages.read-all'))->assertNoContent();
        $this->assertNotNull(Message::sole()->message_read_at);
        $this->getJson(route('student.messages.unread-count'))->assertJsonPath('count', 0);
    }
}
