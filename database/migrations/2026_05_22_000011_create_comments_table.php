<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->tinyInteger('rating')->nullable();
            $table->text('content');
            $table->enum('status', ['visible', 'hidden', 'pending', 'rejected'])->default('visible');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('comments'); }
};