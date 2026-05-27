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
        Schema::table('panoramas', function (Blueprint $table) {
            $table->decimal('initial_yaw', 8, 4)->default(0);
            $table->decimal('initial_pitch', 8, 4)->default(0);
            $table->decimal('initial_fov', 8, 4)->default(90);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panoramas', function (Blueprint $table) {
            $table->dropColumn(['initial_yaw', 'initial_pitch', 'initial_fov']);
        });
    }
};
