<?php

use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $departments = [
            [
                'id' => 1,
                'name' => 'Accounting',
                'allow_spareparts' => false,
            ],
            [
                'id' => 2,
                'name' => 'Marketing',
                'allow_spareparts' => false,
            ],
            [
                'id' => 3,
                'name' => 'Sales',
                'allow_spareparts' => false,
            ],
            [
                'id' => 4,
                'name' => 'CSI',
                'allow_spareparts' => true,
            ],
            [
                'id' => 5,
                'name' => 'Installaction',
                'allow_spareparts' => true,
            ],
            [
                'id' => 6,
                'name' => 'ECTCC',
                'allow_spareparts' => true,
            ],
            [
                'id' => 7,
                'name' => 'Logistics',
                'allow_spareparts' => true,
            ],
            [
                'id' => 8,
                'name' => 'Services',
                'allow_spareparts' => true,
            ],
            [
                'id' => 9,
                'name' => 'HR',
                'allow_spareparts' => false,
            ]
        ];

        \App\Department::insert($departments);
    }
}
