<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Skin extends Model
{
    protected $table = 'skins';
    protected $hidden = ["created_at", "updated_at"];
    
}
