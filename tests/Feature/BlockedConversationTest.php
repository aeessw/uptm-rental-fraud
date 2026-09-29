<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlockedConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_settings_manage_only_your_blocks(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student', 'user_name' => 'Blocked Contact']);
        $third = User::factory()->create(['user_role' => 'student']);
        $third->blockedUsers()->attach($other);
        $this->actingAs($student)->post(route('student.users.block', $other))->assertRedirect();
        $this->post(route('student.users.block', $other))->assertRedirect();
        $this->get(route('student.profile'))->assertOk()->assertSee('Blocked Users (1)')->assertSee('Blocked Contact');
        $this->post(route('student.users.unblock', $other))->assertRedirect();
        $this->get(route('student.profile'))->assertOk()->assertSee('You have no blocked users.');
        $this->assertDatabaseHas('user_blocks', ['blocker_id' => $third->getKey(), 'blocked_id' => $other->getKey()]);
        $this->assertDatabaseMissing('user_blocks', ['blocker_id' => $student->getKey(), 'blocked_id' => $other->getKey()]);
        $this->post(route('student.users.block', $student))->assertForbidden();
    }

    public function test_blocked_recipient_cannot_send_or_unblock_the_other_person(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student']);
        $other->blockedUsers()->attach($student);
        $this->actingAs($student)->get(route('student.messages', $other->getKey()))
            ->assertOk()->assertSee('Messaging is unavailable for this conversation.')
            ->assertDontSee('Type your message...')->assertDontSee('>Unblock<', false);
        $this->post(route('student.messages.store'), ['receiver_id' => $other->getKey(), 'message' => 'Hello'])
            ->assertSessionHas('error', 'Messaging is unavailable for this conversation.');
        $this->post(route('student.messages.unblock', $other->getKey()))->assertRedirect();
        $this->assertDatabaseHas('user_blocks', ['blocker_id' => $other->getKey(), 'blocked_id' => $student->getKey()]);
        $this->assertDatabaseCount('messages', 0);
    }

    public function test_owner_of_block_can_unblock_and_restore_composer(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student']);
        $student->blockedUsers()->attach($other);
        $this->actingAs($student)->get(route('student.messages', $other->getKey()))
            ->assertOk()->assertSee('You blocked this user.')->assertDontSee('Type your message...');
        $this->post(route('student.messages.unblock', $other->getKey()))->assertRedirect();
        $this->get(route('student.messages', $other->getKey()))->assertOk()->assertSee('Type your message...');
    }
}
