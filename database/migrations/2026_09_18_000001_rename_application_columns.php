<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const COLUMNS = [
        'users' => ['id' => 'user_id', 'name' => 'user_name', 'email' => 'user_email', 'role' => 'user_role', 'suspended' => 'user_suspended', 'created_at' => 'user_created_at', 'updated_at' => 'user_updated_at'],
        'listings' => ['id' => 'listing_id', 'title' => 'listing_title', 'description' => 'listing_description', 'location' => 'listing_location', 'rent' => 'listing_rent', 'photo' => 'listing_photo', 'status' => 'listing_status', 'availability' => 'listing_availability', 'created_at' => 'listing_created_at', 'updated_at' => 'listing_updated_at'],
        'listing_photos' => ['id' => 'photo_id', 'created_at' => 'photo_created_at', 'updated_at' => 'photo_updated_at'],
        'messages' => ['id' => 'message_id', 'message' => 'message_content', 'read_at' => 'message_read_at', 'created_at' => 'message_created_at', 'updated_at' => 'message_updated_at'],
        'reports' => ['id' => 'report_id', 'reason' => 'report_reason', 'created_at' => 'report_created_at', 'updated_at' => 'report_updated_at'],
        'audit_logs' => ['id' => 'audit_id', 'action' => 'audit_action', 'target' => 'audit_target', 'hmac' => 'audit_hmac', 'created_at' => 'audit_created_at', 'updated_at' => 'audit_updated_at'],
        'listing_saves' => ['id' => 'save_id', 'created_at' => 'save_created_at', 'updated_at' => 'save_updated_at'],
        'user_blocks' => ['id' => 'block_id', 'created_at' => 'block_created_at', 'updated_at' => 'block_updated_at'],
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
