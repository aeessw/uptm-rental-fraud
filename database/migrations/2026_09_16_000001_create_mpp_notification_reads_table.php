<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('mpp_notification_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('notification_key');
            $table->unique(['user_id', 'notification_key']);
        });
    }
    public function down(): void { Schema::dropIfExists('mpp_notification_reads'); }
};
