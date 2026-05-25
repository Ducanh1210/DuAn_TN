<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password_hash');
            $table->string('email', 120)->unique();
            $table->string('display_name', 120)->nullable();
            $table->text('avatar_url')->nullable();
            $table->enum('role', ['user', 'moderator', 'admin'])->default('user');
            $table->enum('status', ['active', 'locked', 'deleted'])->default('active');
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->dateTime('last_login')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('users'); }
};