<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('geographic_boundaries', function (Blueprint $table) {
            $table->id();
            $table->string('region_name', 120);
            $table->json('boundary_geojson');
            $table->enum('status', ['active', 'hidden'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('geographic_boundaries'); }
};