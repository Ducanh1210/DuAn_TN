<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('panorama_hotspots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panorama_id')->constrained('panoramas')->cascadeOnDelete();
            $table->foreignId('target_panorama_id')->nullable()->constrained('panoramas')->nullOnDelete();
            $table->string('title', 120)->nullable();
            $table->enum('hotspot_type', ['scene', 'info', 'link'])->default('scene');
            $table->decimal('yaw', 8, 4)->nullable();
            $table->decimal('pitch', 8, 4)->nullable();
            $table->text('content')->nullable();
            $table->text('link_url')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('panorama_hotspots'); }
};