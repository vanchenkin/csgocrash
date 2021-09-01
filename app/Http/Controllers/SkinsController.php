<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Skin;
use App\RSkin;
use DB;

class SkinsController extends Controller
{

    public function get(Request $request){
        $min = 0;
        $min = (int)max($min, $request->input('min', 0));
        $max = 1e9;
        $max = (int)min($max, $request->input('max', 1e9));
        $query = '';
        $query = $request->input('query', '');
    	return Skin::where('price', '<=', $max)->where('price', '>=', $min)->where('name', 'LIKE', '%'.$query.'%')->orderBy('price', 'DESC')->simplePaginate(10);
    }

    public function buy(Request $request){
        $user = $request->user();
        $skins = json_decode($request->input('skins'));
        $sell = json_decode($request->input('sell'));
        foreach ($sell as $skin) {
            $tmp = $user->skins()->where('id', $skin)->first();
            if(!is_numeric($skin) || !$tmp || $tmp->bets()->where('status', 'ingame')->count())
                return response()->json(['text' => 'Error. 2', 'type' => 'error']);
        }
        foreach ($skins as $skin) {
            if(!Skin::where('id', $skin->id)->exists())
                return response()->json(['text' => 'Error. 3', 'type' => 'error']);
            $tskin = Skin::find($skin->id);
            if($tskin->price != $skin->price)
                return response()->json(['text' => 'Error. 4', 'type' => 'error']);
        }
        $addSkins = [];
        $delSkins = [];
        DB::transaction(function () use ($sell, $skins, $user, &$addSkins, &$delSkins) {
            $sum = 0;
            foreach ($sell as $skin) {
                $rskin = RSkin::find($skin);
                $sum += $rskin->price;
                array_push($delSkins, ['id' => $skin]);
                $rskin->delete();
            }
            foreach ($skins as $skin) {
                $tskin = Skin::find($skin->id);
                $rskin = new Rskin;
                $rskin->user_id = $user->id;
                $rskin->skin_id = $tskin->id;
                $rskin->price = $tskin->price;
                $rskin->save();
                $tskin = $rskin->skin;
                $tskin->id = $rskin->id;
                array_push($addSkins, $tskin);
                $sum -= $rskin->price;
            }
            if($user->money + $sum < 0)  throw new Exception();
            $user->money += $sum;
            $user->save();
        });
        return response()->json(['text' => 'Completed.', 'type' => 'success', 'add' => $addSkins, 'del' => $delSkins, 'money' => $user->money]);
    }
}
