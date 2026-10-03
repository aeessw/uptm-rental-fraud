<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('listing_notifications', function (Blueprint $table) {
            $table->string('decision')->default('approved');
            $table->string('reason')->nullable();
        });
    }
    public function down(): void {
        Schema::table('listing_notifications', fn (Blueprint $table) => $table->dropColumn(['decision', 'reason']));
    }
};
