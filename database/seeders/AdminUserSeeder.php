<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'password_hash' => md5('123456'),
            'email' => 'admin@example.com',
            'display_name' => 'Administrator',
            'role' => 'admin',
            'status' => 'active',
        ]);
    }
}
