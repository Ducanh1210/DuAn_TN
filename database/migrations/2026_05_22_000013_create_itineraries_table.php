<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('itineraries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('title', 200);
            $table->integer('total_days')->unsigned();
            $table->text('description')->nullable();
            $table->string('share_token', 64)->unique()->nullable();
            $table->boolean('is_public')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('itineraries'); }
};