<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\{User, Listing, Report, AuditLog};
use Illuminate\Foundation\Testing\RefreshDatabase;
class MppStudentInvestigationTest extends TestCase
{
    use RefreshDatabase;
    public function test_investigation_and_reasoned_suspension(): void
    {
        $mpp = User::factory()->create(['user_role' => 'mpp']);
        $student = User::factory()->create(['user_role' => 'student']);
        $other = User::factory()->create(['user_role' => 'student']);
        $room = Listing::create(['user_id' => $student->getKey(), 'listing_title' => 'Student room', 'listing_description' => 'Room', 'listing_location' => 'Cheras', 'listing_rent' => 450, 'room_type' => 'Single', 'listing_status' => 'active']);
        Report::create(['user_id' => $other->getKey(), 'listing_id' => $room->getKey(), 'report_reason' => 'Suspicious']);
        $this->actingAs($mpp)->get(route('mpp.students'))->assertOk()->assertSee('Reports Received')->assertSee('student-details-'.$student->getKey())->assertViewHas('users', fn ($users) => $users->firstWhere('user_id', $student->getKey())->received_reports_count === 1 && $users->firstWhere('user_id', $student->getKey())->listings_count === 1);
        $this->get(route('mpp.listings', ['user_id' => $other->getKey()]))->assertOk()->assertViewHas('listings', fn ($items) => $items->isEmpty());
        $this->get(route('mpp.dashboard'))->assertOk();
        $this->get(route('mpp.students.show', $student))->assertOk()->assertSee('Account overview');
        $this->get(route('mpp.students', ['student' => $student->getKey()]))->assertOk()->assertSee('student-details-'.$student->getKey());
        $this->get(route('mpp.reports', ['user_id' => $student->getKey()]))->assertOk()->assertViewHas('reports', fn ($reports) => $reports->count() === 1);
        $this->get(route('mpp.reports', ['user_id' => $other->getKey()]))->assertOk()->assertViewHas('reports', fn ($reports) => $reports->isEmpty());
        $this->get(route('mpp.reports'))->assertOk()->assertSee('Suspend Account')->assertSee(route('mpp.users.suspend', $student))->assertSee('suspend-owner-'.$room->getKey());
        $this->post(route('mpp.users.suspend', $student))->assertSessionHasErrors('reason');
        $this->assertFalse($student->fresh()->user_suspended);
        $this->post(route('mpp.users.suspend', $student), ['reason' => 'Other'])->assertSessionHasErrors('note');
        $this->post(route('mpp.users.suspend', $student), ['reason' => 'Repeated suspicious listings', 'note' => 'Reviewed reports', 'hide_listings' => 1])->assertRedirect();
        $this->assertTrue($student->fresh()->user_suspended);
        $this->get(route('mpp.reports'))->assertOk()->assertSee('Account Suspended')->assertDontSee('suspend-owner-'.$room->getKey());
        $this->assertSame('hidden', $room->fresh()->listing_status);
        $log = AuditLog::where('audit_action', 'suspended_user')->firstOrFail();
        $this->assertEquals($mpp->getKey(), $log->user_id);
        $this->assertStringContainsString('Reviewed reports', $log->audit_target);
        $this->get(route('mpp.audit.logs', ['user_id' => $student->getKey()]))->assertOk()->assertViewHas('auditLogs', fn ($logs) => $logs->contains('audit_id', $log->getKey()));
        $this->get(route('mpp.audit.logs', ['user_id' => $other->getKey()]))->assertOk()->assertViewHas('auditLogs', fn ($logs) => $logs->isEmpty());
        $this->post(route('mpp.users.unsuspend', $student))->assertRedirect();
        $this->assertFalse($student->fresh()->user_suspended);
        $this->assertSame('hidden', $room->fresh()->listing_status);
        $this->post(route('mpp.users.suspend', $mpp), ['reason' => 'Misleading information'])->assertNotFound();
        $this->actingAs($other)->post(route('mpp.users.suspend', $student), ['reason' => 'Misleading information'])->assertRedirect('/');
    }
}
