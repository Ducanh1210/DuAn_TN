<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung cột payload / estimated_cost còn thiếu trên bảng itineraries.
 * Phải chạy TRƯỚC migration enhance_itineraries_for_ai_payload (dùng after('payload')).
 * Idempotent: chỉ thêm khi cột chưa tồn tại.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            if (!Schema::hasColumn('itineraries', 'estimated_cost')) {
                $table->string('estimated_cost', 120)->nullable()->after('description');
            }
            if (!Schema::hasColumn('itineraries', 'payload')) {
                $table->json('payload')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('itineraries', function (Blueprint $table) {
            foreach (['payload', 'estimated_cost'] as $col) {
                if (Schema::hasColumn('itineraries', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
