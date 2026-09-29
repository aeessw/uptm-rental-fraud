<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->index(['sender_id', 'id']);
            $table->index(['receiver_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropIndex(['messages_sender_id_id_index']);
            $table->dropIndex(['messages_receiver_id_id_index']);
        });
    }
};
