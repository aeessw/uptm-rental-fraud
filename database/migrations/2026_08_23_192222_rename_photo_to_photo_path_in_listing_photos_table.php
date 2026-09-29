<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('listing_photos', 'photo') && ! Schema::hasColumn('listing_photos', 'photo_path')) {
            Schema::table('listing_photos', function (Blueprint $table) {
                $table->renameColumn('photo', 'photo_path');
            });
        }
    }

    public function down(): void
    {
        Schema::table('listing_photos', function (Blueprint $table) {
            $table->renameColumn('photo_path', 'photo');
        });
    }
};