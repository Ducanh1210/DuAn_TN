<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('verification_photo')->nullable()->after('storefront_photos');
            $table->decimal('verification_lat', 10, 7)->nullable()->after('verification_photo');
            $table->decimal('verification_lng', 10, 7)->nullable()->after('verification_lat');
            $table->timestamp('verification_time')->nullable()->after('verification_lng');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'verification_photo',
                'verification_lat',
                'verification_lng',
                'verification_time'
            ]);
        });
    }
};
