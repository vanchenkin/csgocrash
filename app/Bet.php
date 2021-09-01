<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
	protected $table = 'bets';
    protected $hidden = ["created_at", "updated_at"];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function skins()
    {
        return $this->belongsToMany('App\RSkin', 'bet_skin')->withTimestamps();
    }

    public function calcBet(){
        $sum = 0;
        foreach ($this->skins()->withoutGlobalScopes()->get() as $skin)
            $sum += $skin->price;
        return $sum;
    }

    public function winSkin(){
        $winAmount = $this->number*$this->calcBet();
        $skin = Skin::where('price', '<=', $winAmount)->orderBy('price', 'desc')->first();
        $skin->price = $winAmount;
        return $skin;
    }
}