<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->string('name', 200);
            $table->string('slug', 255)->unique();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->text('detailed_history')->nullable();
            $table->text('address')->nullable();
            $table->string('ward', 120)->nullable();
            $table->string('district', 120)->nullable();
            $table->string('province', 120)->default('Hà Nam');
            $table->decimal('lat', 10, 7);
            $table->decimal('lng', 10, 7);
            $table->text('opening_hours')->nullable();
            $table->string('phone', 30)->nullable();
            $table->text('website_url')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->json('attributes')->nullable(); // For amenities/tags
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->integer('review_count')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->bigInteger('view_count')->default(0)->unsigned();
            $table->enum('source', ['admin', 'community'])->default('admin');
            $table->enum('status', ['draft', 'published', 'hidden', 'pending'])->default('published');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['lat', 'lng']);
        });
    }
    public function down(): void { Schema::dropIfExists('locations'); }
};