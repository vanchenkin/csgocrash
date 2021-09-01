<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 2; $i++)
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
        for ($i = 0; $i < 50; $i++){
            DB::table('messages')->insert([
                'user_id' => 1,
                'text' => Str::random(60),
                'created_at' => Carbon::now(),
            ]);
        }
        $rarity = ['white', 'lightblue', 'blue', 'purple', 'pink', 'red', 'knife'];
        $randomSkinsArray = [
            'http://localhost/csgocrash/img/skin1.png',
            'http://localhost/csgocrash/img/skin2.png',
            'http://localhost/csgocrash/img/skin3.png',
            'http://localhost/csgocrash/img/skin4.png',
            'http://localhost/csgocrash/img/skin5.png',
            'http://localhost/csgocrash/img/skin6.png',
            'http://localhost/csgocrash/img/skin7.png',
            'http://localhost/csgocrash/img/skin8.png',
            'http://localhost/csgocrash/img/skin9.png',
        ];
        for ($i = 0; $i < 500; $i++){
            DB::table('skins')->insert([
                'weapon' => Str::random(20),
                'name' => Str::random(20),
                'quality' => 'WW',
                'stattrak' => (rand(0,1) == 1),
                'rarity' => $rarity[rand(0, count($rarity)-1)],
                'image' => $randomSkinsArray[array_rand($randomSkinsArray)],
                'price' => mt_rand(2, 1000),
            ]);
        }
        // DB::table('draws')->insert([
        //     'skin_id' => 1,
        //     'time' => 1800,
        //     'created_at' => Carbon::now(),
        // ]);
        DB::table('tickets')->insert([
            'user_id' => 1,
            'name' => "12321",
            'text' => "12321",
            'created_at' => Carbon::now(),
        ]);
    }
}
