<?php

use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
   public function run()
{
    \App\Models\User::updateOrCreate(
        ['email' => 'admin@soglomavlod.uz'],
        [
            'name' => 'Admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'school_id' => 1,
            'email_verified_at' => now(),
        ]
    );
}
}
