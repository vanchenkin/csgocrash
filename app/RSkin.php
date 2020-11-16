<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RSkin extends Model
{
    protected $table = 'rskins';
    protected $hidden = ["created_at", "updated_at"];
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('deleted', function (Builder $builder) {
            $builder->where('deleted', false);
        });
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function skin()
    {
        return $this->belongsTo('App\Skin');
    }
    public function bets()
    {
        return $this->belongsToMany('App\Bet', 'bet_skin')->withTimestamps();
    }
    public function ingame()
    {
        return ($this->bets()->where('status', 'ingame')->count() > 0)?true:false;
    }
}