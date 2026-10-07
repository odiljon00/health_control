<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder {
    public function run() {
        $adminData = config('auth-seed.admin');

        User::updateOrCreate(
            ['email' => $adminData['email']],
            [
                'name'      => $adminData['name'],
                'password'  => Hash::make($adminData['password']),
                'role'      => $adminData['role'],
                'school_id' => null,   // Admin maktabga bog'lanmagan
                'is_active' => true,
            ]
        );

        $this->command->info("✅ Admin yaratildi: {$adminData['email']}");
    }
}