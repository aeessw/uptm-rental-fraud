<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\{User, Listing, Report};
use Illuminate\Foundation\Testing\RefreshDatabase;
class DuplicateReportTest extends TestCase
{
    use RefreshDatabase;
    public function test_report_that_hides_listing_redirects_to_success_popup(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $reporter = User::factory()->create(['user_role' => 'student']);
        $listing = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active', 'review_status' => 'approved']);
        foreach (User::factory()->count(2)->create(['user_role' => 'student']) as $earlierReporter) {
            Report::create(['listing_id' => $listing->getKey(), 'user_id' => $earlierReporter->getKey(), 'report_reason' => 'Suspicious']);
        }
        $this->actingAs($reporter)->from(route('student.listings.show', $listing))
            ->post(route('student.listings.report', $listing), ['reason' => 'Suspicious'])
            ->assertRedirect(route('student.listings'))->assertSessionHas('success', 'Listing reported successfully.');
        $this->get(route('student.listings'))->assertOk()->assertSee('submission-success-dialog')->assertSee('Listing reported successfully.');
        $this->assertSame('hidden', $listing->fresh()->listing_status);
        $this->get(route('student.listings'))->assertDontSee('submission-success-dialog');
    }
    public function test_duplicate_submission_preserves_report_and_counter(): void
    {
        $owner = User::factory()->create(['user_role' => 'student']);
        $reporter = User::factory()->create(['user_role' => 'student']);
        $listing = Listing::create(['user_id' => $owner->getKey(), 'listing_title' => 'Room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active', 'review_status' => 'approved', 'report_count' => 0]);
        $this->actingAs($reporter)->post(route('student.listings.report', $listing), ['reason' => 'Original reason'])->assertSessionHas('success');
        $this->post(route('student.listings.report', $listing), ['reason' => 'Replacement reason'])->assertSessionHas('error');
        $this->assertSame(1, Report::count());
        $this->assertSame('Original reason', Report::first()->report_reason);
        $this->assertEquals(1, $listing->fresh()->report_count);
        $this->actingAs(User::factory()->create(['user_role' => 'student']))->post(route('student.listings.report', $listing), ['reason' => 'Independent report'])->assertSessionHas('success');
        $this->assertSame(2, Report::count());
    }
}
