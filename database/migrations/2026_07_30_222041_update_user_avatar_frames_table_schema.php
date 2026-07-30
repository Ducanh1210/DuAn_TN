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
        Schema::table('user_avatar_frames', function (Blueprint $table) {
            $table->dropForeign(['frame_id']);
            $table->renameColumn('frame_id', 'avatar_frame_id');
            $table->renameColumn('is_active', 'is_equipped');
            $table->timestamp('unlocked_at')->nullable()->after('is_active'); // after will use old name during migration run, wait, actually better to put after('is_equipped') but just don't specify after for safety
        });

        Schema::table('user_avatar_frames', function (Blueprint $table) {
            $table->foreign('avatar_frame_id')->references('id')->on('avatar_frames')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_avatar_frames', function (Blueprint $table) {
            $table->dropForeign(['avatar_frame_id']);
            $table->renameColumn('avatar_frame_id', 'frame_id');
            $table->renameColumn('is_equipped', 'is_active');
            $table->dropColumn('unlocked_at');
        });

        Schema::table('user_avatar_frames', function (Blueprint $table) {
            $table->foreign('frame_id')->references('id')->on('avatar_frames')->onDelete('cascade');
        });
    }
};
