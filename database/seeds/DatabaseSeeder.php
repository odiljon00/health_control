<?php
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run() {
        $this->call([
            SchoolSeeder::class,    // ← 1-chi: maktablar
            UserSeeder::class,      // ← 2-chi: foydalanuvchilar
            StudentSeeder::class,   // ← 3-chi: o'quvchilar
        ]);
    }
}