<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudentNotificationTest extends TestCase
{
    use RefreshDatabase;

    private function notification(User $owner, string $title): int
    {
        $listing = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => $title,
            'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450,
            'room_type' => 'Single', 'listing_status' => 'hidden', 'review_status' => 'rejected']);
        return DB::table('listing_notifications')->insertGetId([
            'user_id' => $owner->getKey(), 'listing_id' => $listing->getKey(),
            'notification_decision' => 'rejected', 'notification_reason' => 'Incomplete information',
            'notification_created_at' => now()->subHours(2),
        ], 'notification_id');
    }

    public function test_read_notifications_remain_visible_and_badge_only_counts_unread(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $key = $this->notification($owner, 'My room');
        $this->actingAs($owner)->get(route('student.notifications'))->assertOk()
            ->assertSee('My room')->assertSee('Incomplete information')->assertSee('Select all on this page');
        $this->getJson(route('student.notifications'))->assertJsonPath('count', 1);
        $this->post(route('student.notifications.read'), ['key' => $key])->assertRedirect(route('student.notifications'));
        $this->get(route('student.notifications'))->assertOk()->assertSee('My room')->assertDontSee('Unread notification')->assertDontSee('Mark as read');
        $this->getJson(route('student.notifications'))->assertJsonPath('count', 0);
        $this->assertDatabaseCount('listing_notifications', 1);
        $listingId = DB::table('listing_notifications')->where('notification_id', $key)->value('listing_id');
        $this->post(route('student.notifications.read'), ['key' => $key, 'open' => 1])
            ->assertRedirect(route('student.listings.show', $listingId));
        $this->get(route('student.listings.show', $listingId))->assertOk();
    }

    public function test_selection_removes_only_selected_owned_notifications(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student']);
        $remove = $this->notification($owner, 'Remove this');
        $keep = $this->notification($owner, 'Keep this');
        $foreign = $this->notification($other, 'Private room');
        $this->actingAs($owner)->get(route('student.notifications'))->assertDontSee('Private room');
        $this->post(route('student.notifications.read'), ['key' => $foreign, 'open' => 1])->assertNotFound();
        $this->post(route('student.notifications.read'))->assertRedirect();
        $this->assertDatabaseHas('listing_notifications', ['notification_id' => $foreign, 'notification_read_at' => null]);
        $this->delete(route('student.notifications.destroy'), ['keys' => [$remove, $foreign]])->assertRedirect(route('student.notifications'));
        $this->assertDatabaseMissing('listing_notifications', ['notification_id' => $remove]);
        $this->assertDatabaseHas('listing_notifications', ['notification_id' => $keep]);
        $this->assertDatabaseHas('listing_notifications', ['notification_id' => $foreign]);
        $this->deleteJson(route('student.notifications.destroy'), [])->assertUnprocessable();
        $this->assertDatabaseCount('listing_notifications', 2);
    }
}
