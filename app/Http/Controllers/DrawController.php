<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Draw;
use Illuminate\Support\Facades\Redis;

class DrawController extends Controller
{
	public $draw;

	public function __construct()
    {
        $this->draw = Draw::all()->last();
        if($this->draw->time + $this->draw->created_at->timestamp - Carbon::now()->timestamp <= 0)
            $this->draw->associate($this->draw->users()->random());
    }

    public function take(Request $request){
    	if(!$this->draw->users()->where('user_id', $request->user()->id)->count())
    		$this->draw->users()->attach($request->user());
    	Redis::publish('draw.count', json_encode($this->draw->users()->count()));
    	return response()->json(['text' => 'OK', 'type' => 'success']);
    }
}