<?php

namespace Tests\Feature;

use App\Models\Listing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NotificationColumnRenameMigrationTest extends TestCase
{
    public function test_populated_tables_preserve_data_constraints_and_auto_increment_on_rename_and_rollback(): void
    {
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
        $this->artisan('migrate')->assertExitCode(0);
        $migration = require database_path('migrations/2026_09_30_000002_rename_notification_columns.php');
        $migration->down();
        $user = User::factory()->create();
        $listing = Listing::create([
            'user_id' => $user->getKey(), 'listing_title' => 'Existing room',
            'listing_description' => 'Room', 'listing_location' => 'Cheras',
            'listing_rent' => 450, 'room_type' => 'Single',
        ]);
        $rows = [
            'listing_notifications' => [
                'id' => 141, 'user_id' => $user->getKey(), 'listing_id' => $listing->getKey(),
                'decision' => 'rejected', 'reason' => 'Existing reason',
                'created_at' => '2026-09-01 12:00:00', 'read_at' => '2026-09-02 12:00:00',
            ],
            'mpp_notification_reads' => [
                'id' => 241, 'user_id' => $user->getKey(), 'notification_key' => 'report:101',
            ],
        ];
        $columns = (new \ReflectionClass($migration))->getConstant('COLUMNS');
        $indexes = $foreignKeys = [];
        foreach ($rows as $table => $row) {
            DB::table($table)->insert($row);
            $indexes[$table] = Schema::getIndexes($table);
            $foreignKeys[$table] = Schema::getForeignKeys($table);
        }
        $migration->up();
        foreach ($columns as $table => $renames) {
            $expected = [];
            foreach ($rows[$table] as $key => $value) {
                $expected[$renames[$key] ?? $key] = $value;
            }
            $this->assertEquals($expected, (array) DB::table($table)->sole());
            foreach ($renames as $old => $new) {
                $this->assertFalse(Schema::hasColumn($table, $old));
                $this->assertTrue(Schema::hasColumn($table, $new));
            }
            $expectedIndexes = array_map(function ($index) use ($renames) {
                $index['columns'] = array_map(fn ($column) => $renames[$column] ?? $column, $index['columns']);
                return $index;
            }, $indexes[$table]);
            $this->assertEquals($expectedIndexes, Schema::getIndexes($table));
            $this->assertEquals($foreignKeys[$table], Schema::getForeignKeys($table));
            $column = collect(Schema::getColumns($table))->firstWhere('name', $renames['id']);
            $this->assertTrue($column['auto_increment']);
            $next = $expected;
            unset($next[$renames['id']]);
            if ($table === 'mpp_notification_reads') {
                $this->assertSame(0, DB::table($table)->insertOrIgnore($next));
                $next['notification_read_key'] = 'report:102';
            }
            $this->assertGreaterThan($expected[$renames['id']], DB::table($table)->insertGetId($next, $renames['id']));
        }
        $this->assertSame([], DB::select('PRAGMA foreign_key_check'));
        $beforeRollback = [];
        foreach ($columns as $table => $renames) {
            $beforeRollback[$table] = DB::table($table)->orderBy($renames['id'])->get()->map(function ($row) use ($renames) {
                $reverse = array_flip($renames);
                $mapped = [];
                foreach ((array) $row as $key => $value) {
                    $mapped[$reverse[$key] ?? $key] = $value;
                }
                return $mapped;
            })->all();
        }
        $migration->down();
        foreach ($columns as $table => $renames) {
            $this->assertEquals($beforeRollback[$table], DB::table($table)->orderBy('id')->get()->map(fn ($row) => (array) $row)->all());
            $this->assertEquals($indexes[$table], Schema::getIndexes($table));
            $this->assertEquals($foreignKeys[$table], Schema::getForeignKeys($table));
            $column = collect(Schema::getColumns($table))->firstWhere('name', 'id');
            $this->assertTrue($column['auto_increment']);
        }
        $this->assertSame([], DB::select('PRAGMA foreign_key_check'));
        $migration->up();
    }
}
