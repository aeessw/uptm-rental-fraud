<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('listing_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete();
            $table->unsignedBigInteger('listing_id');
            $table->foreign('listing_id')->references('listing_id')->on('listings')->cascadeOnDelete();
            $table->timestamp('created_at');
            $table->timestamp('read_at')->nullable();
        });
    }
    public function down(): void { Schema::dropIfExists('listing_notifications'); }
};
