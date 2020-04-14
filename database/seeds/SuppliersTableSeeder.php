<?php

use Illuminate\Database\Seeder;
use App\Supplier;

class SuppliersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $suppliers = [
            [
                'name' => 'LG',
            ],
            [
                'name' => 'Electrolux',
            ]
        ];

        Supplier::insert($suppliers);
    }
}
