<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->timestamp('sender_deleted_at')->nullable();
            $table->timestamp('receiver_deleted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn(['sender_deleted_at', 'receiver_deleted_at']);
        });
    }
};
