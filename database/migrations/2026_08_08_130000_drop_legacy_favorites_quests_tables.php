<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Gỡ các bảng cũ không còn dùng:
 * - favorites: bị thay bằng favorite_locations.
 * - quests / user_quests: hệ thống cũ, đã thay bằng missions / user_missions
 *   (không còn model, route hay truy vấn nào; đều rỗng).
 */
return new class extends Migration
{
    private array $legacyTables = ['favorites', 'quests', 'user_quests'];

    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($this->legacyTables as $table) {
            Schema::dropIfExists($table);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        // Các bảng cũ không tái tạo (đã bị thay thế hoàn toàn).
    }
};
