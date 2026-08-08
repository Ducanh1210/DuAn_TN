<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung các cột gamification còn thiếu migration trên bảng users.
 * Idempotent: chỉ thêm khi cột chưa tồn tại.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'equipped_frame_id')) {
                $table->unsignedBigInteger('equipped_frame_id')->nullable()->after('points');
            }
            if (!Schema::hasColumn('users', 'streak_count')) {
                $table->integer('streak_count')->default(0)->after('equipped_frame_id');
            }
            if (!Schema::hasColumn('users', 'last_streak_at')) {
                $table->timestamp('last_streak_at')->nullable()->after('streak_count');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            foreach (['equipped_frame_id', 'streak_count', 'last_streak_at'] as $col) {
                if (Schema::hasColumn('users', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
