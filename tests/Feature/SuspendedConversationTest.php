<?php

namespace Tests\Feature;

use App\Helpers\EncryptionHelper;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuspendedConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_suspension_preserves_history_and_reporting_but_disables_composer_and_room_link(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $owner = User::factory()->create(['user_role' => 'student', 'user_suspended' => true]);
        $listing = Listing::create([
            'user_id' => $owner->getKey(), 'listing_title' => 'Evidence room',
            'listing_description' => 'Room description', 'listing_location' => 'Cheras',
            'listing_rent' => 400, 'room_type' => 'Single Room', 'listing_status' => 'hidden',
        ]);
        Message::create([
            'sender_id' => $owner->getKey(), 'receiver_id' => $student->getKey(),
            'listing_id' => $listing->getKey(), 'message_content' => EncryptionHelper::encrypt('Preserved evidence'),
        ]);
        $this->actingAs($student)->get(route('student.message.inbox'))
            ->assertOk()->assertSee($owner->user_name)->assertSee('Account Suspended');
        $this->get(route('student.messages', $owner->getKey()))
            ->assertOk()->assertSee('Preserved evidence')->assertSee('Account Suspended')
            ->assertSee('Room unavailable')->assertSee('Report user or listing')
            ->assertDontSee('id="message-compose-form"', false)
            ->assertDontSee('href="'.route('student.listings.show', $listing->getKey()).'"', false);
        $this->post(route('student.listings.report', $listing), ['reason' => 'Suspicious deposit request'])
            ->assertSessionHas('success');
        $this->assertDatabaseCount('reports', 1);
        $this->assertDatabaseCount('messages', 1);
    }

    public function test_suspended_sender_and_recipient_are_rejected_and_restoration_allows_sending(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student', 'user_suspended' => true]);
        $this->actingAs($student)->postJson(route('student.messages.store'), [
            'receiver_id' => $other->getKey(), 'message' => 'Cannot receive',
        ])->assertForbidden();
        $this->post(route('student.messages.store'), [
            'receiver_id' => $other->getKey(), 'message' => 'Cannot receive from form',
        ])->assertSessionHas('error');
        $this->actingAs($other)->postJson(route('student.messages.store'), [
            'receiver_id' => $student->getKey(), 'message' => 'Cannot send',
        ])->assertForbidden();
        $this->get(route('student.messages', $student->getKey()))
            ->assertOk()->assertSee('Your Account Is Suspended')->assertDontSee('id="message-compose-form"', false);
        $this->assertDatabaseCount('messages', 0);
        $other->update(['user_suspended' => false]);
        $this->actingAs($student)->get(route('student.messages', $other->getKey()))
            ->assertOk()->assertSee('id="message-compose-form"', false);
        $this->postJson(route('student.messages.store'), [
            'receiver_id' => $other->getKey(), 'message' => 'Restored',
        ])->assertCreated();
        $this->assertDatabaseCount('messages', 1);
    }
}
