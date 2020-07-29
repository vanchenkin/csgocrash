<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'avatar', 'steamid', 'steamid64',
    ];

    protected $hidden = [
        'remember_token', 'created_at', 'updated_at',
    ];

    public function games()
    {
        return $this->hasMany('App\Game');
    }

    public function bets()
    {
        return $this->hasMany('App\Bet');
    }

    public function skins()
    {
        return $this->hasMany('App\RSkin');
    }

}
