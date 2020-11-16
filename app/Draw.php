<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    protected $table = 'draws';
    public function skin()
    {
        return $this->belongsTo('App\Skin');
    }
    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps();
    }
    public function winner()
    {
        return $this->belongsTo('App\User', 'winner_id');
    }
}
