<?php
use App\School;
use App\Student;
use App\User;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder {
    public function run() {
        $school = School::where('name', "1-son umumta'lim maktabi")->first();
        $parent = User::where('email', 'parent@soglomavlod.uz')->first();

        if (!$school) {
            $this->command->error("Maktab topilmadi! Avval SchoolSeeder ishga tushiring.");
            return;
        }

       
    }
}