<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('panorama_hotspots', function (Blueprint $table) {
            $table->decimal('target_yaw', 8, 4)->nullable()->after('target_panorama_id');
            $table->decimal('target_pitch', 8, 4)->nullable()->after('target_yaw');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panorama_hotspots', function (Blueprint $table) {
            $table->dropColumn(['target_yaw', 'target_pitch']);
        });
    }
};
