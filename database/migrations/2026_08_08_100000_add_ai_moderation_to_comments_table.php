<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            // AI moderation result: safe | suspect | violation
            $table->string('ai_flag', 20)->nullable()->after('status');
            // Risk score 0-100 (higher = riskier)
            $table->unsignedTinyInteger('ai_score')->nullable()->after('ai_flag');
            // Short reason from the AI
            $table->string('ai_reason', 500)->nullable()->after('ai_score');
            // When the comment was last checked by AI
            $table->timestamp('ai_checked_at')->nullable()->after('ai_reason');
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn(['ai_flag', 'ai_score', 'ai_reason', 'ai_checked_at']);
        });
    }
};
