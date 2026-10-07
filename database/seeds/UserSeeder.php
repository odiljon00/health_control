<?php
use App\School;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder {
    public function run() {
        $school = School::where('name', "1-son umumta'lim maktabi")->first();

        User::updateOrCreate(
            ['email' => 'odil@soglomavlod.uz'],
            [
                'name'     => 'Administrator',
                'password' => Hash::make('OdiljonAdmin.01'),
                'role'     => 'admin',
                'school_id'=> null,
            ]
        );

        User::updateOrCreate(
            ['email' => 'teacher@soglomavlod.uz'],
            [
                'name'      => 'Nodira Karimova',
                'password'  => Hash::make('ustozlar'),
                'role'      => 'teacher',
                'school_id' => $school ? $school->id : null,
                'phone'     => '+998901112233',
            ]
        );

        User::updateOrCreate(
            ['email' => 'psixolog@soglomavlod.uz'],
            [
                'name'      => 'Dilshod Rahimov',
                'password'  => Hash::make('psixolog'),
                'role'      => 'psychologist',
                'school_id' => $school ? $school->id : null,
                'phone'     => '+998902223344',
            ]
        );

       

        User::updateOrCreate(
            ['email' => 'iio@soglomavlod.uz'],
            [
                'name'      => 'Kapitan Bobur Aliyev',
                'password'  => Hash::make('iio'),
                'role'      => 'iio',
                'school_id' => null,
            ]
        );
    }
}