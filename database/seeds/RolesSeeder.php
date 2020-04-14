<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'id' => 1,
                'name' => 'developer',
                'description' => 'Mostly for backoffice',
                'level' => 'developer',
            ],
            [
                'id' => 2,
                'name' => 'admin',
                'description' => 'This is awesome',
                'level' => 'client',
            ],
        ];

        \App\Role::insert($roles);
    }
}
