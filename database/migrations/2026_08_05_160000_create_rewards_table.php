<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('category', 32); // voucher, badge, exclusive
            $table->unsignedInteger('cost_points');
            $table->string('image')->nullable();
            $table->unsignedInteger('stock')->nullable();
            $table->string('status', 16)->default('active');
            $table->timestamps();
        });

        Schema::create('user_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reward_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('points_spent');
            $table->string('status', 16)->default('completed');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_redemptions');
        Schema::dropIfExists('rewards');
    }
};
