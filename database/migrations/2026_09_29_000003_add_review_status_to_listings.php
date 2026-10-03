<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('review_status')->default('pending');
        });
        // Release posts held by the previous approval-before-publishing workflow.
        DB::table('listings')->where('listing_status', 'pending')->update(['listing_status' => 'active']);
    }

    public function down(): void
    {
        Schema::table('listings', fn (Blueprint $table) => $table->dropColumn('review_status'));
    }
};
