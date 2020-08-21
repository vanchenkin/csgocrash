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
        if($request->has('min'))
            $min = $request->input('min');
        $max = 1e9;
        if($request->has('max'))
            $max = $request->input('max');
        $name = '';
        if($request->has('name'))
            $name = $request->input('name');
    	return Skin::where('price', '<=', $max)->where('price', '>=', $min)->orderBy('price', 'DESC')->simplePaginate(10);
    }

    public function buy(Request $request){
        $user = $request->user();
        if($request->has('skins'))
            $skins = json_decode($request->input('skins'));
        else
            return response()->json(['text' => 'Бdddубу. Бебеб', 'type' => 'error']);
        $sell = json_decode($request->input('sell'));
        foreach ($sell as $skin) {
            if(!$user->skins()->where('id', $skin)->exists() || RSkin::find($skin)->status == 'bet')
                return response()->json(['text' => 'adadБубу. Бебеб', 'type' => 'error']);
        }
        foreach ($skins as $skin) {
            if(!Skin::where('id', $skin->id)->exists())
                return response()->json(['text' => 'ddadadaБубу. Бебеб', 'type' => 'error']);
            $tskin = Skin::find($skin->id);
            if($tskin->price != $skin->price)
                return response()->json(['text' => 'dadaddadadaБубу. Бебеб', 'type' => 'error']);
        }
        $addSkins = array();
        $delSkins = array();
        DB::transaction(function () use ($sell, $skins, $user, &$addSkins, &$delSkins) {
            $sum = 0;
            foreach ($sell as $skin) {
                $rskin = RSkin::find($skin);
                $sum += $rskin->price;
                array_push($delSkins, $skin);
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
            if($user->money + $sum < 0)  throw new Exception('Money less than zero');
            $user->money += $sum;
            $user->save();
        });
        return response()->json(['text' => 'Действие выполнено.', 'type' => 'success', 'add' => $addSkins, 'del' => $delSkins, 'money' => $user->money]);
    }
}
