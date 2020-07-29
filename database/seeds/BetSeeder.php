<?php

use Illuminate\Database\Seeder;

class BetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	DB::table('games')->insert([
            'number' => 3,
            'salt' => 'Adadadadadwafagrgfefw',
        ]);
        DB::table('bets')->insert([
            'user_id' => 1,
            'game_id' => 1,
            'number' => 2,
        ]);
        DB::table('bet_skin')->insert([
            'bet_id' => 1,
            'r_skin_id' => 1,
        ]);
    }
}
