<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('feedback_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('report_type', ['wrong_info', 'duplicate_location', 'image_error', 'wrong_position', 'location_closed', 'system_suggestion', 'other']);
            $table->enum('target_type', ['location', 'news', 'event', 'comment', 'system'])->nullable();
            $table->bigInteger('target_id')->unsigned()->nullable();
            $table->text('content');
            $table->enum('status', ['pending', 'processing', 'resolved', 'rejected'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->foreignId('resolved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('resolved_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('feedback_reports'); }
};