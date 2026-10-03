<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNS = [
        'listing_notifications' => [
            'id' => 'notification_id',
            'decision' => 'notification_decision',
            'reason' => 'notification_reason',
            'read_at' => 'notification_read_at',
            'created_at' => 'notification_created_at',
        ],
        'mpp_notification_reads' => [
            'id' => 'notification_read_id',
            'notification_key' => 'notification_read_key',
        ],
    ];

    public function up(): void
    {
        $this->renameColumns(self::COLUMNS);
    }

    public function down(): void
    {
        $this->renameColumns(array_map('array_flip', array_reverse(self::COLUMNS, true)));
    }

    private function renameColumns(array $tables): void
    {
        foreach ($tables as $tableName => $columns) {
            foreach ($columns as $from => $to) {
                Schema::table($tableName, function (Blueprint $table) use ($from, $to) {
                    $table->renameColumn($from, $to);
                });
            }
        }
    }
};
