<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * إنشاء مستخدم لوحة التحكم الرئيسي.
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'admin@personalcare.test',
            ],
            [
                'name' => 'Administrator',
                'password' => Hash::make('Admin@123456'),
                'email_verified_at' => now(),
            ]
        );
    }
}