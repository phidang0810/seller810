<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * @param $social
     * @return mixed
     */
    public function login($social)
    {
        $driver = Socialite::driver($social);
        return $driver->redirect();
    }

    /**
     * Obtain the user information from Social Logged in.
     *
     * @param SocialUserRepository $userRepo
     * @param $social
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function handleProviderCallback(Request $request, UserRepository $userRepo,  $social)
    {
        //$url = $request->get('url');

        try {
           // $redirect = config('services.'.$social . '.redirect');
            //config(['services.'.$social . '.redirect' => $redirect . '?url=' . $url]);

            $userSocial = Socialite::driver($social)->user();

            $user = $userRepo->checkAccountExist($userSocial, $social);
            $userID = null;
            if($user){
                $userID = $user->id;
            }
            $user = $userRepo->createOrUpdateSocialUser($social, $userSocial, $userID);
            Auth::login($user);
            return view('auth.redirect', [
                'auth' => $user
            ]);
        } catch (\Exception $e) {
            die($e->getMessage());
            return view('auth.redirect_cancel');
        }
    }

    public function logout(Request $request) {
        if(!Auth::check()) {
            return view('errors.401');
        }

        Auth::logout();
        return redirect()->back();
    }
}
