<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates the single account used for the technical test.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate(
            ['username' => 'aldmic'],
            [
                'name' => 'Aldmic',
                'password' => Hash::make('123abc123'),
            ]
        );
    }
}
