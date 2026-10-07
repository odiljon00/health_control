<?php
use App\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder {
    public function run() {


        School::updateOrCreate(
            ['name' => 'IIO Ixtisoslashtirilgan litseyi'],
            [
                'region'   => 'Jizzax viloati',
                'district' => 'Jizzax shahri',
            ]
        );
    }
}