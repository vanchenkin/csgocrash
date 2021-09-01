<?php

namespace App\Http\Controllers;

use Invisnik\LaravelSteamAuth\SteamAuth;
use App\User;
use Auth;

class SteamController extends Controller
{
    protected $steam;

    /**
     * The redirect URL.
     */
    protected $redirectURL = '/';

    /**
     * AuthController constructor.
     */
    public function __construct(SteamAuth $steam)
    {
        $this->steam = $steam;
    }

    /**
     * Redirect the user to the authentication page
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function redirectToSteam()
    {
        return $this->steam->redirect();
    }

    /**
     * Get user info and log in
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handle()
    {
        if ($this->steam->validate()) {
            $info = $this->steam->getUserInfo();

            if (!is_null($info)) {
                $user = $this->findOrNewUser($info);

                Auth::login($user, true);

                return redirect($this->redirectURL);
            }
        }
        return $this->redirectToSteam();
    }

    /**
     * Getting user by info or created if not exists
     *
     * @param $info
     * @return User
     */
    protected function findOrNewUser($info)
    {
        $user = User::where('steamid64', $info->steamID64)->first();
        if (!is_null($user))
            return $user;
        $s = new SteamID($info->steamID64);
        return User::create([
            'name' => $info->personaname,
            'image' => $info->avatarfull,
            'steamid64' => $info->steamID64,
            'steamid' => $info->steamid,
            'steamLink' => $info->profileurl
        ]);
    }

    public function logout() {
		Auth::logout();
		return redirect($this->redirectURL);
	}
}