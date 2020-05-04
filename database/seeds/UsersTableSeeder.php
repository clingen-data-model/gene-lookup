<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'tester',
                'email' => 'test@example.org',
                'password' => Hash::make('tester')
            ]
        );
    }
}
