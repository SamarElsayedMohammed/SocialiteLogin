<?php

namespace App\Http\Controllers;

use App\DTO\UserDTO;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
    public function ProviderRedirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function ProviderCallBack($provider)
    {
        $userSocial = Socialite::driver($provider)->user();

        // dd($userSocial);
        $users = User::where(['email' => $userSocial->getEmail()])->first();
        if ($users) {
            Auth::login($users);
            return redirect()->route('dashboard');
        } else {
            $userDTO = new UserDTO(
                $userSocial->getName(),
                $userSocial->getEmail(),
                '123',
                $userSocial->token ?? '',
                $userSocial->getAvatar(),
                $userSocial->getId(),
                $userSocial->getId(),
                $provider,
            );
            $user = $this->userService->createUser($userDTO);
            return redirect()->route('dashboard');
        }
    }

}
