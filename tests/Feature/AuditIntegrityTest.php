<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Models\User;
use App\Helpers\AuditLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
class AuditIntegrityTest extends TestCase
{
    use RefreshDatabase;
    public function test_signature_verification_and_audit_display(): void
    {
        config(['audit.hmac_key' => 'test-signing-key']);
        $mpp = User::factory()->create(['user_role' => 'mpp']);
        $log = AuditLogger::log($mpp->getKey(), 'viewed_listing', 'Listing ID: 12');
        $this->assertSame('Valid', $log->fresh()->integrityStatus());
        $this->actingAs($mpp)->get(route('mpp.audit.logs'))->assertOk()->assertSee('Listing #12')->assertSee('Integrity')->assertSee('View Details')->assertDontSee('System activity recorded.');
        $log->update(['audit_target' => 'Listing ID: 13']);
        $this->assertSame('Invalid', $log->fresh()->integrityStatus());
        config(['audit.hmac_key' => null]);
        $this->assertSame('Unavailable', $log->integrityStatus());
        $log->audit_action = 'sent_message'; $log->audit_target = 'Message: private words';
        $this->assertStringNotContainsString('private words', $log->displayDetails());
    }
}
