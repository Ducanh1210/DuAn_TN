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
        Schema::table('avatar_frames', function (Blueprint $table) {
            $table->string('code')->nullable()->after('name');
            $table->string('type')->default('rank')->after('code');
            $table->integer('required_points')->default(0)->after('type');
            $table->text('css_style')->nullable()->after('required_points');
            $table->string('icon')->nullable()->after('image_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('avatar_frames', function (Blueprint $table) {
            $table->dropColumn(['code', 'type', 'required_points', 'css_style', 'icon']);
        });
    }
};
