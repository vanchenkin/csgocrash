<?php

namespace App\Http\Controllers;

use App\User;
use App\Game;
use App\Bet;
use App\Skin;
use App\RSkin;
use App\Message;
use App\Draw;
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

	public function debug($id)
    {
        $user = User::find($id);
        Auth::login($user, true);
        return redirect('/');
    }

    public function generateNumber(){
        //return 1.06;
        return mt_rand(1, 3).".". mt_rand(0, 9). mt_rand(0, 9);
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
        $game->salt = Str::random(15);
		$game->number = $this->generateNumber();
		$game->save();
        $this->game = $game;
        return [
            'id' => $game->id,
            'number' => $game->number,
            'hash' => $game->hash(),
            'salt' => $game->salt,
        ];
    }

	public function finishGame(){
	    $bets = $this->game->bets()->get();
		$profit = 0;
        $winSkins = [];
		foreach ($bets as $bet){
            $user = $bet->user;
            $bet->skins()->where('user_id', $user->id)->update(['deleted' => true]);
            if($bet->number <= $this->game->number && $bet->number != 0){
                $bet->status = 'win';
                $win = $bet->winSkin();
                $t = new RSkin;
                $t->user()->associate($user);
                $t->skin()->associate($win);
                $t->price = $win->price;
                $t->save();
                $profit += $win->price;
                array_push($winSkins, [
                    'user_id' => $user->id,
                    'skin_id' => $t->id,
                ]);
            }else{
                $bet->status = 'lose';
            }
            $bet->save();
		}
		$this->game->profit = $this->game->calcBets() - $profit;
		$this->game->status = 'finished';
		$this->game->save();
        return response()->json($winSkins);
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
			return response()->json(['text' => 'Error. 1 time.', 'type' => 'error']);
        if (!$request->has('skins'))
        	return response()->json(['text' => 'Error. no skins', 'type' => 'error']);
        if ($this->game->status != 'created')
        	return response()->json(['text' => 'Wait new game!', 'type' => 'error']);
        $skins = json_decode($request->input('skins'));
        $skins = array_unique($skins);
        if (!count($skins))
            return response()->json(['text' => 'Error. no skins', 'type' => 'error']);
		$number = (float)$request->input('number');
		if (!is_numeric($number) || ($number <= 1 && $number != 0))
			return response()->json(['text' => 'Error. wrong number', 'type' => 'error']);
		foreach ($skins as $skin) {
			if(!is_numeric($skin) || !$request->user()->skins()->where('id', $skin)->exists())
				return response()->json(['text' => 'Error. not owning skin', 'type' => 'error']);
		}
        $bet = new Bet;
        $bet->user()->associate($request->user());
        $bet->game()->associate($this->game);
        $bet->number = $number;
        $bet->save();
        $tskins = [];
        foreach ($skins as $skin) {
            $rskin = RSkin::find($skin);
            $bet->skins()->attach($rskin->id);
            $rskin->save();
            array_push($tskins, [
                'id' => $rskin->id,
                'weapon' => $rskin->skin->weapon,
                'name' => $rskin->skin->name,
                'quality' => $rskin->skin->quality,
                'stattrak' => $rskin->skin->stattrak,
                'rarity' => $rskin->skin->rarity,
                'image' => $rskin->skin->image,
                'price' => $rskin->price,
                'ingame' => $rskin->ingame(),
            ]);
        }
        Redis::publish('crash.bet', json_encode([
            'id' => $bet->id,
            'number' => $bet->number,
			'skins' => $tskins,
			'sum' => $bet->calcBet(),
            'user' => [
                'id' => $request->user()->id,
                'name' => $request->user()->name,
                'image' => $request->user()->image,
            ],
            'win' => $bet->winSkin(),
            'status' => 'ingame',
        ]));
        return response()->json(['text' => 'OK', 'type' => 'success']);
    }

    public function getWin(Request $request){
        if(!$request->has('sum') || !is_numeric($request->input('sum')))
            return response()->json(['text' => 'Error. No amount', 'type' => 'error']);
        if(!$request->has('number') || !is_numeric($request->input('number')) || $request->input('number') <= 1)
            return response()->json(['text' => 'Error. No number', 'type' => 'error']);
        return response()->json($this->winSkin((float)$request->input('number') * (float)$request->input('sum')));
    }

    public function winSkin($win){
        return Skin::where('price', '<=', $win)->orderBy('price', 'desc')->first();
    }

    public function cancelBet(Request $request){
        $bet = $this->game->bets()->where('user_id', $request->user()->id)->first();
        if (!$bet)
            return response()->json(['text' => 'Error. No bets.', 'type' => 'error']);
        Redis::publish('crash.cancel', json_encode([
            'id' => $bet->id,
        ]));
        $bet->delete();
        return response()->json(['text' => 'OK', 'type' => 'success']);
    }

    public function stopBet(Request $request){
    	$number = (float)$request->input('number');
    	if (!is_numeric($number) || $number <= 1)
        	return response()->json(['text' => 'Error. ', 'type' => 'error']);
        $bet = $this->game->bets()->where('user_id', $request->user()->id)->first();
        if ($number > $bet->number && $bet->number != 0)
        	return response()->json(['text' => 'Error.  Игра завершена', 'type' => 'error']);
        $bet->number = $number;
        $bet->save();
        Redis::publish('crash.stop', json_encode([
            'id' => $bet->id,
            'number' => $number,
            'win' => $bet->winSkin(),
        ]));
        return response()->json(['text' => 'OK', 'type' => 'success']);
    }

    public function index(Request $request)
    {
        $game = [
            'id' => $this->game->id,
            'hash' => $this->game->hash(),
            'status' => $this->game->status,
        ];
        $games = Game::where('status', 'finished')->orderBy('id', 'desc')->take(20)->get();
        $tbets = $this->game->bets()->with('user')->get();

        $bets = [];
        foreach ($tbets as $bet) {
            $skins = $bet->skins()->with('skin')->get();
            $tskins = [];
            foreach ($skins as $skin) {
                array_push($tskins, [
                    'id' => $skin->id,
                    'weapon' => $skin->skin->weapon,
                    'name' => $skin->skin->name,
                    'quality' => $skin->skin->quality,
                    'stattrak' => $skin->skin->stattrak,
                    'rarity' => $skin->skin->rarity,
                    'image' => $skin->skin->image,
                    'price' => $skin->price,
                    'ingame' => $skin->ingame(),
                ]);
            }
            $user = $bet->user;
            array_push($bets, [
                'id' => $bet->id,
                'number' => $bet->number,
                'skins' => $tskins,
                'sum' => $bet->calcBet(),
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image' => $user->image,
                ],
                'win' => $bet->winSkin(),
                'status' => $bet->status,
            ]);
        }
        $skins = [];
        $tickets = [];
        $money = 0;
        if(Auth::check()){
            $money = Auth::user()->money;
            $tickets = $request->user()->tickets()->get();
            $tskins = $request->user()->skins()->with('skin')->get();
            foreach ($tskins as $skin) {
                array_push($skins, [
                    'id' => $skin->id,
                    'weapon' => $skin->skin->weapon,
                    'name' => $skin->skin->name,
                    'quality' => $skin->skin->quality,
                    'stattrak' => $skin->skin->stattrak,
                    'rarity' => $skin->skin->rarity,
                    'image' => $skin->skin->image,
                    'price' => $skin->price,
                    'ingame' => $skin->ingame(),
                ]);
            }
        }
        $tm = Message::orderBy('created_at', 'desc')->limit(30)->get();
        $messages = [];
        foreach($tm as $t){
            $user = $t->user;
            array_push($messages, [
                'id' => $t->id,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image' => $user->image,
                ],
                'time' => $t->created_at?$t->created_at->format('H:i'):null,
                'text' => $t->text,
            ]);
        }
        $messages = array_reverse($messages);
        $d = Draw::all()->last();
        $draw = [];
        $ld = Draw::orderBy('created_at', 'desc')->skip(1)->first();
        if($d)
            $draw = [
                'id' => $d->id,
                'skin' => $d->skin,
                'time' => $d->time + $d->created_at->timestamp - Carbon::now()->timestamp,
                'take' => (Auth::check() && $d->users()->where('user_id', Auth::user()->id)->count()),
                'count' => $d->users()->count(),
                'last_winner' => ($ld && $ld->winner)?['id' => $ld->winner->id,'image' => $ld->winner->image]:null,
            ];
        return view('main', compact('game', 'bets', 'skins', 'games', 'money', 'messages', 'draw', 'tickets'));
    }
}