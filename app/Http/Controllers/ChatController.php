<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Message;
use Illuminate\Support\Facades\Redis;

class ChatController extends Controller
{
	public function send(Request $request){
		if(!$request->input('text') || strlen($request->input('text')) > 100)
			return response()->json(['text' => 'error', 'type' => 'error']);
		$mes = new Message;
		$mes->text = $request->input('text');
		$mes->user()->associate($request->user());
		$mes->save();
		Redis::publish('chat.message', json_encode([
            'id' => $mes->id,
            'user' => [
                'id' => $mes->user->id,
                'name' => $mes->user->name,
                'image' => $mes->user->image,
            ],
            'time' => $mes->created_at?$mes->created_at->format('H:i'):null,
            'text' => $mes->text,
        ]));
    	return response()->json(['text' => 'OK', 'type' => 'success']);
    }
}