<?php

namespace Tests\Feature;

use App\Helpers\EncryptionHelper;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_deletion_only_hides_the_requesting_users_history_and_new_messages_return(): void
    {
        $alice = User::factory()->create(['user_role' => 'student']);
        $bob = User::factory()->create(['user_role' => 'student']);
        $charlie = User::factory()->create(['user_role' => 'student']);
        foreach ([[$alice, $bob, 'Old outgoing text'], [$bob, $alice, 'Old incoming text'], [$charlie, $alice, 'Unrelated text']] as [$sender, $receiver, $text]) {
            Message::create(['sender_id' => $sender->getKey(), 'receiver_id' => $receiver->getKey(), 'message_content' => EncryptionHelper::encrypt($text)]);
        }
        $this->actingAs($alice)->post(route('student.messages.delete', $bob->getKey()))
            ->assertRedirect(route('student.message.inbox'));
        $this->assertDatabaseCount('messages', 3);
        $this->get(route('student.message.inbox'))->assertOk()->assertDontSee($bob->user_email)->assertSee('Unrelated text');
        $this->get(route('student.messages', $bob->getKey()))->assertOk()->assertDontSee('Old outgoing text')->assertDontSee('Old incoming text');
        $this->getJson(route('student.messages.unread-count'))->assertJsonPath('count', 1);
        $this->actingAs($bob)->get(route('student.messages', $alice->getKey()))
            ->assertOk()->assertSee('Old outgoing text')->assertSee('Old incoming text');
        $this->getJson(route('student.messages.unread-count'))->assertJsonPath('count', 1);
        $this->post(route('student.messages.store'), ['receiver_id' => $alice->getKey(), 'message' => 'Fresh message'])->assertRedirect();
        $this->actingAs($alice)->get(route('student.messages', $bob->getKey()))
            ->assertOk()->assertSee('Fresh message')->assertDontSee('Old outgoing text')->assertDontSee('Old incoming text');
        $this->post(route('student.messages.delete', $bob->getKey()))->assertRedirect();
        $this->actingAs($bob)->post(route('student.messages.delete', $alice->getKey()))->assertRedirect();
        $this->get(route('student.messages', $alice->getKey()))->assertOk()->assertDontSee('Fresh message')->assertDontSee('Old outgoing text');
        $this->assertDatabaseCount('messages', 4);
    }
}
