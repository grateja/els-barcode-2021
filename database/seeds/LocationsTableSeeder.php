<?php

use Illuminate\Database\Seeder;
use App\Location;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            [
                'name' => 'Main office',
            ],
            [
                'name' => 'Ware House 1',
            ],
            [
                'name' => 'Ware House 2',
            ],
            [
                'name' => 'Ware House 3',
            ],
            [
                'name' => 'Ware House 4',
            ],
            [
                'name' => 'Ware House 5',
            ],
            [
                'name' => 'Client',
            ],
            [
                'name' => 'Supplier',
            ]
        ];
        Location::insert($locations);
    }
}
