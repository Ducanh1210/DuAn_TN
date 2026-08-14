<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->string('public_phone', 30)->nullable()->after('phone');
            $table->string('zalo', 255)->nullable()->after('public_phone');
            $table->string('facebook', 255)->nullable()->after('zalo');
        });

        Schema::table('locations', function (Blueprint $table) {
            if (!Schema::hasColumn('locations', 'zalo')) {
                $table->string('zalo', 255)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('locations', 'facebook')) {
                $table->string('facebook', 255)->nullable()->after('zalo');
            }
        });
    }

    public function down(): void
    {
        Schema::table('business_profiles', function (Blueprint $table) {
            $table->dropColumn(['public_phone', 'zalo', 'facebook']);
        });

        Schema::table('locations', function (Blueprint $table) {
            $cols = array_values(array_filter(['zalo', 'facebook'], fn ($c) => Schema::hasColumn('locations', $c)));
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
};
