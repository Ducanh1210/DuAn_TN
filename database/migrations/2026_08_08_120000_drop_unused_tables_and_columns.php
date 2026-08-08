<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Dọn các bảng chưa từng được sử dụng (chỉ có migration, không model/không truy vấn)
 * và 1 cột orphan trên business_profiles. An toàn: dùng dropIfExists + hasColumn.
 */
return new class extends Migration
{
    private array $unusedTables = [
        'business_contact_leads',
        'contributions',
        'location_claims',
        'business_packages',
        'geographic_boundaries',
        'chat_logs',
        'direction_logs',
        'analytics_logs',
    ];

    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($this->unusedTables as $table) {
            Schema::dropIfExists($table);
        }
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        if (Schema::hasColumn('business_profiles', 'contact_cta_unlocked_at')) {
            Schema::table('business_profiles', function (Blueprint $table) {
                $table->dropColumn('contact_cta_unlocked_at');
            });
        }
    }

    public function down(): void
    {
        // Chỉ khôi phục lại cấu trúc tối thiểu (dữ liệu cũ không phục hồi được).
        if (!Schema::hasColumn('business_profiles', 'contact_cta_unlocked_at')) {
            Schema::table('business_profiles', function (Blueprint $table) {
                $table->timestamp('contact_cta_unlocked_at')->nullable();
            });
        }
        // Các bảng đã gỡ vốn không được sử dụng nên không tái tạo trong down().
    }
};
