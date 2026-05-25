<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('itinerary_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('day_id')->constrained('itinerary_days')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->integer('order_index')->unsigned();
            $table->time('estimated_time')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['day_id', 'order_index']);
            $table->unique(['day_id', 'location_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('itinerary_items'); }
};