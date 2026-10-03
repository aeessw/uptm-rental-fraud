<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->boolean('hidden_by_suspension')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('listings', fn (Blueprint $table) => $table->dropColumn('hidden_by_suspension'));
    }
};
