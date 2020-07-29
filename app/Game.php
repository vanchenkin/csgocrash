<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';
    public $hash;
	public function bets()
    {
        return $this->hasMany('App\Bet');
    }

    public function hash()
    {
        return hash('sha256', $this->number.$this->salt);
    }

    public function calcBets()
    {
        $sum = 0;
        foreach ($this->bets() as $bet) {
            $sum += $bet->calcBet();
        }
        return $sum;
    }

}