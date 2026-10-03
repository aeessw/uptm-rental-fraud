<?php

namespace Tests\Feature;

use App\Helpers\EncryptionHelper;
use App\Models\AuditLog;
use App\Models\Listing;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ColumnRenameMigrationTest extends TestCase
{
    public function test_populated_schema_round_trip_preserves_values_signatures_and_foreign_keys(): void
    {
        // This test uses only the isolated in-memory database configured in phpunit.xml.
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        $this->artisan('migrate')->assertExitCode(0);
        $migration = require database_path('migrations/2026_09_18_000001_rename_application_columns.php');
        $migration->down();

        config(['audit.hmac_key' => 'rename-test-signing-key']);
        $date = '2026-08-01 12:34:56';
        $timestamps = ['created_at' => $date, 'updated_at' => $date];
        $ciphertext = EncryptionHelper::encrypt('Existing encrypted message');
        $signature = hash_hmac('sha256', '41'.'viewed_listing'.'Listing ID: 71'.$date, config('audit.hmac_key'));
        DB::table('users')->insert([
            ['id' => 41, 'name' => 'Owner', 'email' => 'owner@example.test', 'role' => 'student'] + $timestamps,
            ['id' => 42, 'name' => 'Viewer', 'email' => 'viewer@example.test', 'role' => 'student'] + $timestamps,
        ]);
        DB::table('listings')->insert(['id' => 71, 'user_id' => 41, 'title' => 'Existing room', 'description' => 'Description', 'location' => 'Cheras', 'rent' => 550, 'room_type' => 'Single', 'photo' => 'existing.jpg'] + $timestamps);
        DB::table('listing_photos')->insert(['id' => 81, 'listing_id' => 71, 'photo_path' => 'existing.jpg'] + $timestamps);
        DB::table('messages')->insert(['id' => 91, 'sender_id' => 41, 'receiver_id' => 42, 'listing_id' => 71, 'message' => $ciphertext, 'read_at' => $date] + $timestamps);
        DB::table('reports')->insert(['id' => 101, 'user_id' => 42, 'listing_id' => 71, 'reason' => 'Existing report'] + $timestamps);
        DB::table('audit_logs')->insert(['id' => 111, 'user_id' => 41, 'action' => 'viewed_listing', 'target' => 'Listing ID: 71', 'hmac' => $signature] + $timestamps);
        DB::table('listing_saves')->insert(['id' => 121, 'user_id' => 42, 'listing_id' => 71] + $timestamps);
        DB::table('user_blocks')->insert(['id' => 131, 'blocker_id' => 42, 'blocked_id' => 41] + $timestamps);
        DB::table('mpp_notification_reads')->insert(['notification_read_id' => 141, 'user_id' => 41, 'notification_read_key' => 'report:101']);

        $columns = (new \ReflectionClass($migration))->getConstant('COLUMNS');
        $before = [];
        foreach (array_keys($columns) as $table) {
            $before[$table] = DB::table($table)->orderBy('id')->get()->map(fn ($row) => (array) $row)->all();
        }
        $migration->up();
        foreach ($columns as $table => $renames) {
            foreach ($renames as $old => $new) {
                $this->assertFalse(Schema::hasColumn($table, $old));
                $this->assertTrue(Schema::hasColumn($table, $new));
            }
            $expected = array_map(function ($row) use ($renames) {
                $mapped = [];
                foreach ($row as $key => $value) $mapped[$renames[$key] ?? $key] = $value;
                return $mapped;
            }, $before[$table]);
            $this->assertEquals($expected, DB::table($table)->orderBy($renames['id'])->get()->map(fn ($row) => (array) $row)->all());
        }
        $this->assertSame($ciphertext, Message::findOrFail(91)->message_content);
        $this->assertSame('Existing encrypted message', EncryptionHelper::decrypt(Message::findOrFail(91)->message_content));
        $this->assertSame('Valid', AuditLog::findOrFail(111)->integrityStatus());
        $listing = Listing::findOrFail(71);
        $this->assertSame(41, $listing->user->getKey());
        $this->assertSame(81, $listing->photos->sole()->getKey());
        $this->assertSame(101, $listing->reports->sole()->getKey());
        $this->assertSame(71, User::findOrFail(42)->savedListings->sole()->getKey());
        $this->assertSame(41, User::findOrFail(42)->blockedUsers->sole()->getKey());
        $this->assertSame($date, User::findOrFail(42)->savedListings->sole()->pivot->save_created_at->toDateTimeString());
        $this->assertSame([], DB::select('PRAGMA foreign_key_check'));
        foreach (['sessions', 'jobs', 'job_batches', 'failed_jobs'] as $table) {
            $this->assertTrue(Schema::hasColumn($table, 'id'));
        }
        $this->assertTrue(Schema::hasColumn('password_reset_tokens', 'email'));

        $migration->down();
        foreach ($before as $table => $rows) {
            $this->assertEquals($rows, DB::table($table)->orderBy('id')->get()->map(fn ($row) => (array) $row)->all());
        }
        $this->assertSame([], DB::select('PRAGMA foreign_key_check'));
        $migration->up();

        $listing->delete();
        $this->assertDatabaseCount('listing_photos', 0);
        $this->assertDatabaseCount('reports', 0);
        $this->assertDatabaseCount('listing_saves', 0);
        $this->assertNull(Message::findOrFail(91)->listing_id);
        User::findOrFail(41)->delete();
        $this->assertDatabaseCount('messages', 0);
        $this->assertDatabaseCount('user_blocks', 0);
        $this->assertDatabaseCount('mpp_notification_reads', 0);
        $this->assertNull(AuditLog::findOrFail(111)->user_id);
    }
}
