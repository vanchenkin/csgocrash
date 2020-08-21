<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => Str::random(10),
            'image' => 'https://steamcdn-a.akamaihd.net/steamcommunity/public/images/avatars/79/791ae00b033de099deb4a51c008359974e8731e1_full.jpg',
            'steamid64' => '',
            'steamid' => '',
            'steamLink' => '',
            //'accessSalt' => Str::random(32),
            'money' => 1000,
            'role' => 'admin',
        ]);
    }
}
