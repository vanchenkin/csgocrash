<?php

use Illuminate\Database\Seeder;

class SkinsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $rarity = ['white', 'lightblue', 'blue', 'purple', 'pink', 'red', 'knife'];
        for ($i = 0; $i < 50; $i++){
            DB::table('skins')->insert([
                'weapon' => Str::random(20),
                'name' => Str::random(20),
                'quality' => 'WW',
                'stattrak' => (rand(0,1) == 1),
                'rarity' => $rarity[rand(0, count($rarity)-1)],
                'image' => 'http://localhost/csgocrash/img/skin.png',
                'price' => mt_rand(2, 100),
            ]);
        }
        for ($i = 1; $i < 6; $i++){
            DB::table('rskins')->insert([
                'user_id' => 1,
                'skin_id' => $i,
                'price' => '15',
            ]);
        }
    }
}
