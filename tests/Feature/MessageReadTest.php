<?php
namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Message;
use App\Helpers\EncryptionHelper;

class MessageReadTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    public function test_background_send_returns_updated_conversation_without_redirect(): void
    {
        $sender = User::factory()->create(['user_role' => 'student']);
        $recipient = User::factory()->create(['user_role' => 'student']);
        $this->actingAs($sender)->postJson(route('student.messages.store'), [
            'receiver_id' => $recipient->getKey(), 'message' => 'Background message',
        ])->assertCreated()->assertJsonStructure(['html']);
        $this->assertDatabaseCount('messages', 1);
        $this->assertSame('Background message', EncryptionHelper::decrypt(Message::first()->message_content));
        $recipient->blockedUsers()->attach($sender);
        $this->postJson(route('student.messages.store'), [
            'receiver_id' => $recipient->getKey(), 'message' => 'Blocked message',
        ])->assertUnprocessable()->assertJson(['message' => 'Messaging is unavailable for this conversation.']);
        $this->assertDatabaseCount('messages', 1);
    }

    public function test_read_receipts_are_scoped_to_recipient_and_displayed_messages(): void
    {
        $sender = User::factory()->create(['user_name' => 'Sender', 'user_email' => 'sender@test.com', 'user_role' => 'student']);
        $recipient = User::factory()->create(['user_name' => 'Recipient', 'user_email' => 'recipient@test.com', 'user_role' => 'student']);
        $first = Message::create(['sender_id' => $sender->getKey(), 'receiver_id' => $recipient->getKey(), 'message_content' => EncryptionHelper::encrypt('Hello there')]);
        $second = Message::create(['sender_id' => $sender->getKey(), 'receiver_id' => $recipient->getKey(), 'message_content' => EncryptionHelper::encrypt('New message')]);
        $this->actingAs($recipient)->getJson(route('student.messages.unread-count'))->assertJson(['count' => 2]);
        $this->get(route('student.messages', $sender->getKey()))->assertOk()->assertSee('Hello there')->assertDontSee('aria-label="Quick replies"', false)->assertSee('Search this conversation')->assertSee('value="read"', false);
        $this->assertNull($first->fresh()->message_read_at);
        $this->actingAs($sender)->postJson(route('student.messages.read', $sender->getKey()), ['through_id' => $second->getKey()])->assertNoContent();
        $this->assertNull($first->fresh()->message_read_at);
        $this->actingAs($recipient)->postJson(route('student.messages.read', $sender->getKey()), ['through_id' => $first->getKey()])->assertNoContent();
        $this->assertNotNull($first->fresh()->message_read_at);
        $this->assertNull($second->fresh()->message_read_at);
        $this->getJson(route('student.messages.unread-count'))->assertJson(['count' => 1]);
        $this->actingAs($sender)->get(route('student.messages', $recipient->getKey()))->assertOk()->assertSee('title="Read"', false);
    }
}