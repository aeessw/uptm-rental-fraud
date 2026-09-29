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
    public function test_notifications_are_linked_and_read_per_mpp_user(): void
    {
        $student = User::factory()->create(['user_role' => 'student']);
        $mpp = User::factory()->create(['user_role' => 'mpp']);
        $other = User::factory()->create(['user_role' => 'mpp']);
        $listing = Listing::create(['user_id' => $student->getKey(), 'listing_title' => 'Flagged room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'hidden', 'report_count' => 3]);
        $report = Report::create(['listing_id' => $listing->getKey(), 'user_id' => $student->getKey(), 'report_reason' => 'Suspicious']);
        $this->actingAs($mpp)->getJson(route('mpp.notifications'))->assertOk()->assertJsonPath('count', 2)->assertJsonFragment(['url' => route('mpp.reports', ['listing_id' => $listing->getKey()])]);
        $this->postJson(route('mpp.notifications.read'), ['key' => 'report:'.$report->getKey()])->assertOk();
        $this->getJson(route('mpp.notifications'))->assertJsonPath('count', 1);
        $this->postJson(route('mpp.notifications.read'))->assertOk();
        $this->getJson(route('mpp.notifications'))->assertJsonPath('count', 0);
        $this->actingAs($other)->getJson(route('mpp.notifications'))->assertJsonPath('count', 2);
        Report::create(['listing_id' => $listing->getKey(), 'user_id' => $student->getKey(), 'report_reason' => 'Another report']);
        $this->actingAs($mpp)->getJson(route('mpp.notifications'))->assertJsonPath('count', 1);
        $this->actingAs($student)->getJson(route('mpp.notifications'))->assertRedirect('/');
        $this->postJson(route('mpp.notifications.read'))->assertRedirect('/');
    }
}
