<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('listings')->where('review_status', '!=', 'approved')->update([
            'listing_status' => 'hidden', 'hidden_by_suspension' => false,
        ]);
    }

    public function down(): void
    {
        // Do not republish unreviewed posts on rollback.
    }
};
