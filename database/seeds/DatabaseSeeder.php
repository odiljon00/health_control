<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {
    public function run() {
        $this->call([
            SchoolSeeder::class,      // 1. Maktablar
            AdminSeeder::class,       // 2. Admin
            UserSeeder::class,        // 3. Ustoz, psixolog, ota-ona, IIO
            StudentSeeder::class,     // 4. O'quvchilar
        ]);
    }
}