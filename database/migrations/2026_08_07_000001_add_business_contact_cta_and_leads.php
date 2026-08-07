<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('business_profiles', 'contact_cta_unlocked_at')) {
                $table->timestamp('contact_cta_unlocked_at')->nullable()->after('reject_reason');
            }
        });

        Schema::create('business_contact_leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_profile_id')->constrained('business_profiles')->cascadeOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('type', ['contact', 'book_table', 'book_room'])->default('contact');
            $table->string('customer_name', 120);
            $table->string('customer_phone', 30);
            $table->string('note', 500)->nullable();
            $table->enum('status', ['new', 'done'])->default('new');
            $table->timestamps();

            $table->index(['business_profile_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_contact_leads');

        Schema::table('business_profiles', function (Blueprint $table) {
            if (Schema::hasColumn('business_profiles', 'contact_cta_unlocked_at')) {
                $table->dropColumn('contact_cta_unlocked_at');
            }
        });
    }
};
