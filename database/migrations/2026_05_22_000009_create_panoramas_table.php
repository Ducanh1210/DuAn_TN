<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('panoramas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->string('scene_name', 120);
            $table->text('image_url');
            $table->text('audio_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('status', ['active', 'hidden'])->default('active');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('panoramas'); }
};