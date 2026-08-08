<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('avatar_photo')->nullable()->after('storefront_photos');
            $table->json('business_documents')->nullable()->after('avatar_photo');
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn(['avatar_photo', 'business_documents']);
        });
    }
};
