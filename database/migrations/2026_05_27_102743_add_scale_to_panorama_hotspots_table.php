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
            $table->decimal('scale', 4, 2)->default(1.0)->after('pitch');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('panorama_hotspots', function (Blueprint $table) {
            $table->dropColumn('scale');
        });
    }
};
