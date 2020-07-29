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
        for ($i = 0; $i < 50; $i++){
            DB::table('skins')->insert([
                'name' => Str::random(20),
                'quality' => 'WW',
                'stattrak' => false,
                'image' => '',
                'price' => mt_rand(2, 100),
            ]);
        }
        
    }
}
