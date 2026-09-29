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
            $table->string('availability', 20)->default('available')->after('status');
        });

        DB::table('listings')
            ->where('status', 'rented')
            ->update([
                'availability' => 'rented',
                'status' => 'active',
            ]);
    }

    public function down(): void
    {
        DB::table('listings')
            ->where('availability', 'rented')
            ->update(['status' => 'active']);

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('availability');
        });
    }
};
