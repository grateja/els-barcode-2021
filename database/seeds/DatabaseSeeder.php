<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(\RolesSeeder::class);
        $this->call(\UsersSeeder::class);

        DB::table('role_users')->insert([
            [
                'user_id' => 'e026cf14-0093-4de3-8ab2-e13086acb7ac',
                'role_id' => 1
            ],
        ]);
    }
}
