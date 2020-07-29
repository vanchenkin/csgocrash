<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RSkin extends Model
{
    protected $table = 'rskins';
    protected $hidden = ["created_at", "updated_at"];
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function skin()
    {
        return $this->belongsTo('App\Skin');
    }
}