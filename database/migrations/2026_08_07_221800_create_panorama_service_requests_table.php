<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('panorama_service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('contact_name', 120);
            $table->string('phone', 30);
            $table->string('place_name', 180);
            $table->string('place_type', 40)->nullable();
            $table->string('scene_estimate', 40)->nullable();
            $table->string('note', 800)->nullable();
            $table->enum('status', ['pending', 'contacted', 'done', 'cancelled'])->default('pending');
            $table->text('admin_note')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panorama_service_requests');
    }
};
