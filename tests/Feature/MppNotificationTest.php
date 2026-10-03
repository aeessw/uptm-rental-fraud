<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use App\Models\Listing;
use App\Models\Report;
use Illuminate\Foundation\Testing\RefreshDatabase;
class MppNotificationTest extends TestCase
{
    use RefreshDatabase;
    public function test_selected_removal_is_private_and_preserves_source_listings(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $mpp = User::factory()->create(['user_role' => 'mpp']);
        $other = User::factory()->create(['user_role' => 'mpp']);
        $listing = Listing::create(['user_id' => $student->getKey(), 'listing_title' => 'Risk room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'review_status' => 'approved', 'report_count' => 3]);
        $report = Report::create(['listing_id' => $listing->getKey(), 'user_id' => $student->getKey(), 'report_reason' => 'Suspicious']);
        $this->actingAs($mpp)->get(route('mpp.notifications'))->assertOk()->assertSee('toggle-notification-removal')->assertSee('notification-selection-toolbar');
        $this->deleteJson(route('mpp.notifications.destroy'), [])->assertUnprocessable();
        $this->delete(route('mpp.notifications.destroy'), ['keys' => ['risk:'.$listing->getKey()]])->assertRedirect(route('mpp.notifications'));
        $this->getJson(route('mpp.notifications'))->assertJsonPath('count', 1)->assertJsonFragment(['key' => 'report:'.$report->getKey()]);
        $this->assertModelExists($listing);
        $this->assertModelExists($report);
        $this->actingAs($other)->getJson(route('mpp.notifications'))->assertJsonPath('count', 2);
        $this->actingAs($student)->delete(route('mpp.notifications.destroy'), ['keys' => ['risk:'.$listing->getKey()]])->assertRedirect('/');
    }
    public function test_notifications_are_linked_and_read_per_mpp_user(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $mpp = User::factory()->create(['user_role' => 'mpp']);
        $other = User::factory()->create(['user_role' => 'mpp']);
        $listing = Listing::create(['user_id' => $student->getKey(), 'listing_title' => 'Flagged room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'hidden', 'review_status' => 'approved', 'report_count' => 3]);
        $report = Report::create(['listing_id' => $listing->getKey(), 'user_id' => $student->getKey(), 'report_reason' => 'Suspicious']);
        $this->actingAs($mpp)->getJson(route('mpp.notifications'))->assertOk()->assertJsonPath('count', 2)->assertJsonFragment(['url' => route('mpp.reports', ['listing_id' => $listing->getKey()])]);
        $this->get(route('mpp.notifications'))->assertOk()->assertSee('Unread notifications')->assertSee('Flagged room')->assertSee('View report')->assertSee('Review reports')->assertSee('Listing reached 3 reports')->assertSee('fa-triangle-exclamation')->assertSee('fa-flag')->assertDontSee('High-risk listing')->assertDontSee('mpp-notifications-panel');
        $this->postJson(route('mpp.notifications.read'), ['key' => 'report:'.$report->getKey()])->assertOk();
        $this->getJson(route('mpp.notifications'))->assertJsonPath('count', 1);
        $this->post(route('mpp.notifications.read'))->assertRedirect(route('mpp.notifications'));
        $this->get(route('mpp.notifications'))->assertOk()->assertSee('You are all caught up.');
        $this->getJson(route('mpp.notifications'))->assertJsonPath('count', 0);
        $this->actingAs($other)->getJson(route('mpp.notifications'))->assertJsonPath('count', 2);
        Report::create(['listing_id' => $listing->getKey(), 'user_id' => $student->getKey(), 'report_reason' => 'Another report']);
        $this->actingAs($mpp)->getJson(route('mpp.notifications'))->assertJsonPath('count', 1);
        $this->actingAs($student)->getJson(route('mpp.notifications'))->assertRedirect('/');
        $this->postJson(route('mpp.notifications.read'))->assertRedirect('/');
    }

    public function test_approval_notifications_are_private_and_resubmission_notifies_again(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student']);
        $mpp = User::factory()->create(['user_role' => 'mpp']);
        $secondMpp = User::factory()->create(['user_role' => 'mpp']);
        $listing = Listing::create(['user_id' => $student->getKey(), 'listing_title' => 'New room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'hidden', 'review_status' => 'pending']);
        \App\Helpers\AuditLogger::log($student->getKey(), 'created_listing', 'Listing ID: '.$listing->getKey());
        $this->actingAs($mpp)->getJson(route('mpp.notifications'))->assertJsonPath('count', 1)->assertJsonFragment(['url' => route('mpp.listings', ['listing_id' => $listing->getKey()])]);
        $this->get(route('mpp.notifications'))->assertOk()->assertSee('New listing awaiting approval')->assertSee('Review listing')->assertSee('fa-clock')->assertSee('bg-indigo-50 text-indigo-700');
        $this->postJson(route('mpp.notifications.read'))->assertOk();
        $this->getJson(route('mpp.notifications'))->assertJsonPath('count', 0);
        $this->actingAs($secondMpp)->getJson(route('mpp.notifications'))->assertJsonPath('count', 1);
        $this->post(route('mpp.listings.approve', $listing))->assertRedirect();
        $this->getJson(route('mpp.notifications'))->assertJsonPath('count', 0);
        $this->post(route('mpp.listings.approve', $listing))->assertStatus(409);
        $this->assertDatabaseCount('listing_notifications', 1);
        $notificationId = \Illuminate\Support\Facades\DB::table('listing_notifications')->value('notification_id');
        $this->actingAs($other)->getJson(route('student.notifications'))->assertJsonPath('count', 0);
        $this->postJson(route('student.notifications.read'), ['key' => $notificationId])->assertOk();
        $this->actingAs($student)->getJson(route('student.notifications'))->assertJsonPath('count', 1)->assertJsonFragment(['title' => 'Your listing has been approved', 'url' => route('student.listings.show', $listing)]);
        $this->get(route('student.profile'))->assertSee('student-notifications-toggle');
        $this->postJson(route('student.notifications.read'), ['key' => $notificationId])->assertOk();
        $this->getJson(route('student.notifications'))->assertJsonPath('count', 0);
        $listing->refresh()->update(['review_status' => 'pending', 'listing_status' => 'hidden']);
        \App\Helpers\AuditLogger::log($student->getKey(), 'updated_listing', 'Listing ID: '.$listing->getKey());
        $this->actingAs($mpp)->getJson(route('mpp.notifications'))->assertJsonPath('count', 1);
        $this->post(route('mpp.listings.approve', $listing))->assertRedirect();
        $this->actingAs($student)->getJson(route('student.notifications'))->assertJsonPath('count', 1);
        $this->postJson(route('student.notifications.read'))->assertOk();
        $this->getJson(route('student.notifications'))->assertJsonPath('count', 0);
    }

    public function test_owner_receives_rejection_and_later_approval_as_separate_notifications(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student']);
        $mpp = User::factory()->create(['user_role' => 'mpp']);
        $listing = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Owner room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'hidden', 'review_status' => 'pending']);
        $this->actingAs($mpp)->post(route('mpp.listings.reject', $listing), [])->assertSessionHasErrors('reason');
        $this->assertDatabaseCount('listing_notifications', 0);
        $this->post(route('mpp.listings.reject', $listing), ['reason' => 'Misleading information'])->assertRedirect();
        $this->post(route('mpp.listings.reject', $listing), ['reason' => 'Misleading information'])->assertStatus(409);
        $this->assertDatabaseCount('listing_notifications', 1);
        $this->actingAs($other)->getJson(route('student.notifications'))->assertJsonPath('count', 0);
        $this->actingAs($owner)->getJson(route('student.notifications'))->assertJsonPath('count', 1)
            ->assertJsonFragment(['title' => 'Your listing was not approved', 'description' => 'Owner room', 'reason' => 'Misleading information']);
        $this->get(route('student.listings.show', $listing))->assertOk();
        $this->actingAs($mpp)->post(route('mpp.listings.approve', $listing))->assertRedirect();
        $this->actingAs($owner)->getJson(route('student.notifications'))->assertJsonPath('count', 2)
            ->assertJsonFragment(['title' => 'Your listing has been approved'])
            ->assertJsonFragment(['title' => 'Your listing was not approved']);
        $this->postJson(route('student.notifications.read'))->assertOk();
        $this->getJson(route('student.notifications'))->assertJsonPath('count', 0);
    }
}
