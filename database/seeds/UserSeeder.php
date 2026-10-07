<?php

use App\School;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {
        // Maktabni topish
        $school = School::first();

        if (!$school) {
            $this->command->error("❌ Maktab topilmadi! Avval SchoolSeeder ishga tushiring.");
            return;
        }

        $this->command->info("📚 Maktab: {$school->name}");

        // ==================== TEACHER ====================
        $teacherData = config('auth-seed.teacher');
        User::updateOrCreate(
            ['email' => $teacherData['email']],
            [
                'name'      => $teacherData['name'],
                'password'  => Hash::make($teacherData['password']),
                'role'      => $teacherData['role'],
                'school_id' => $school->id,
                'phone'     => $teacherData['phone'],
                'is_active' => true,
            ]
        );
        $this->command->info("✅ Ustoz: {$teacherData['email']}");

        // ==================== PSYCHOLOGIST ====================
        $psychData = config('auth-seed.psychologist');
        User::updateOrCreate(
            ['email' => $psychData['email']],
            [
                'name'      => $psychData['name'],
                'password'  => Hash::make($psychData['password']),
                'role'      => $psychData['role'],
                'school_id' => $school->id,
                'phone'     => $psychData['phone'],
                'is_active' => true,
            ]
        );
        $this->command->info("✅ Psixolog: {$psychData['email']}");

        // ==================== PARENT ====================
        $parentData = config('auth-seed.parent');
        User::updateOrCreate(
            ['email' => $parentData['email']],
            [
                'name'      => $parentData['name'],
                'password'  => Hash::make($parentData['password']),
                'role'      => $parentData['role'],
                'school_id' => $school->id,
                'phone'     => $parentData['phone'],
                'is_active' => true,
            ]
        );
        $this->command->info("✅ Ota-ona: {$parentData['email']}");

        // ==================== IIO ====================
        $iioData = config('auth-seed.iio');
        User::updateOrCreate(
            ['email' => $iioData['email']],
            [
                'name'      => $iioData['name'],
                'password'  => Hash::make($iioData['password']),
                'role'      => $iioData['role'],
                'school_id' => null,   // IIO maktabga bog'lanmagan
                'phone'     => $iioData['phone'],
                'is_active' => true,
            ]
        );
        $this->command->info("✅ IIO: {$iioData['email']}");

        $this->command->info("");
        $this->command->info("🎉 Barcha foydalanuvchilar yaratildi!");
    }
}