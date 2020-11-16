<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Ticket;

class TicketController extends Controller
{
    public function new(Request $request){
    	if($request->user()->tickets()->where('status', 'opened')->count())
    		return response()->json(['text' => 'уже есть тикеты', 'type' => 'error');
    	$t = new Ticket;
    	$t->name = $request->input('name');
    	$t->text = $request->input('text');
    	$t->user()->associate($request->user());
    	$t->save();
    }

    public function get(Request $request){
    	$tickets = $request->user()->tickets()->orderBy('created_at', 'desc')->get();
    	return response()->json($tickets);
    }
}
