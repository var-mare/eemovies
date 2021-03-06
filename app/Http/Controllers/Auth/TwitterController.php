<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Auth;
use Socialite;
use App\User;

class TwitterController extends Controller
{
    protected $redirectPath = '/home';

    public function add()
    {
        return view('guest.login');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('twitter')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $user = Socialite::driver('twitter')->user();
        } catch (Exception $e) {
            return redirect('auth/twitter');
        }

        $authUser = $this->findOrCreateUser($user);
        Auth::login($authUser, true);
        return redirect('/');
    }

    private function findOrCreateUser($twitterUser)
    {
        $authUser = User::where('twitter_id', $twitterUser->id)->first();
        if ($authUser) {
            return $authUser;
        }
        return User::create([
            'name' => $twitterUser->name,
            'nickname' => $twitterUser->nickname,
            'email' => $twitterUser->email,
            'password' => $twitterUser->nickname,
            'twitter_id' => $twitterUser->id,
            'avatar' => $twitterUser->avatar_original
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect("/");
    }
}
