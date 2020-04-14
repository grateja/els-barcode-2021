<?php

use Illuminate\Database\Seeder;
use App\Truck;

class TrucksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $trucks = [
            [
                'name' => 'L300',
                'plate_number' => '000000',
            ],
            [
                'name' => 'Foton',
                'plate_number' => '000000',
            ],
            [
                'name' => '3PL',
                'plate_number' => '000000',
            ],
        ];

        Truck::insert($trucks);
    }
}
