<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter users table to add 'business' role
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'moderator', 'admin', 'business') NOT NULL DEFAULT 'user'");

        // Create business_profiles table
        Schema::create('business_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('business_name');
            $table->json('business_types')->nullable(); // online_retail, local_store, service_business
            $table->unsignedBigInteger('category_id')->nullable(); // Reference to categories table
            $table->string('address_country')->default('Việt Nam');
            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_province')->nullable();
            $table->string('address_postal_code')->nullable();
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->boolean('receive_tips')->default(false);
            $table->boolean('receive_surveys')->default(false);
            $table->text('description')->nullable();
            $table->json('menu_photos')->nullable();
            $table->json('storefront_photos')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('reject_reason')->nullable();
            $table->timestamps();

            // Foreign key for category_id referencing categories.id
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_profiles');
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user', 'moderator', 'admin') NOT NULL DEFAULT 'user'");
    }
};
