<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bổ sung migration còn thiếu cho hệ thống nhiệm vụ / khung avatar.
 * Các bảng này đã tồn tại trên DB hiện tại (tạo tay) nên được bọc hasTable
 * để chạy an toàn (idempotent); trên môi trường mới sẽ tạo đúng schema.
 * Cột `meta` của user_missions do migration add_meta_to_user_missions thêm sau.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('avatar_frames')) {
            Schema::create('avatar_frames', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 50)->unique();
                $table->text('description')->nullable();
                $table->enum('type', ['rank', 'achievement', 'shop'])->default('shop');
                $table->integer('required_points')->default(0);
                $table->string('css_style', 255)->nullable();
                $table->string('image_url', 255)->nullable();
                $table->string('icon', 50)->default('');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('missions')) {
            Schema::create('missions', function (Blueprint $table) {
                $table->id();
                $table->string('title', 200);
                $table->text('description')->nullable();
                $table->enum('type', ['daily', 'weekly', 'achievement', 'special'])->default('daily');
                $table->string('action_key', 50);
                $table->integer('target_count')->default(1);
                $table->integer('reward_points')->default(10);
                $table->foreignId('reward_frame_id')->nullable()->constrained('avatar_frames')->nullOnDelete();
                $table->string('icon', 50)->default('');
                $table->enum('status', ['active', 'inactive'])->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_missions')) {
            Schema::create('user_missions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('mission_id')->constrained('missions')->cascadeOnDelete();
                $table->integer('current_count')->default(0);
                $table->enum('status', ['in_progress', 'completed', 'claimed'])->default('in_progress');
                $table->timestamp('last_reset_at')->nullable();
                $table->timestamp('claimed_at')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'mission_id']);
            });
        }

        if (!Schema::hasTable('user_avatar_frames')) {
            Schema::create('user_avatar_frames', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('avatar_frame_id')->constrained('avatar_frames')->cascadeOnDelete();
                $table->boolean('is_equipped')->default(false);
                $table->timestamp('unlocked_at')->useCurrent();
                $table->timestamps();
                $table->unique(['user_id', 'avatar_frame_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_avatar_frames');
        Schema::dropIfExists('user_missions');
        Schema::dropIfExists('missions');
        Schema::dropIfExists('avatar_frames');
    }
};
