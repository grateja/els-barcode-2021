<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $id = Str::uuid();
        $users = [
            [
                'id' => 'e026cf14-0093-4de3-8ab2-e13086acb7ac',
                'name' => 'The Programmer',
                'email' => 'developer@csi.com',
                'password' => bcrypt('admin'),
            ],
            [
                'id' => $id,
                'name' => 'Admin',
                'email' => 'admin@csi.com',
                'password' => bcrypt('admin'),
            ],
        ];
        \App\User::insert($users);
        User::where('email', 'admin@csi.com')->first()->assignRole(2);
    }
}
