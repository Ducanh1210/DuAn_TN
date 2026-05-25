<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name', 200);
            $table->string('slug', 255)->unique();
            $table->text('description')->nullable();
            $table->text('program')->nullable();
            $table->string('location_text', 255)->nullable();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->dateTime('start_time');
            $table->dateTime('end_time');
            $table->text('featured_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['active', 'cancelled', 'expired', 'hidden'])->default('active');
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('events'); }
};