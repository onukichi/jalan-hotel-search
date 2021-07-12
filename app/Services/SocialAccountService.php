<?php

namespace App\Services;

use Laravel\Socialite\Contracts\User as ProviderUser;
use App\Models\SocialAccount;
use App\Models\User;

class SocialAccountService
{
    public function getOrCreate(ProviderUser $providerUser, $provider)
    {
        $account = SocialAccount::where('provider', $provider)
            ->where('provider_id', $providerUser->getId())
            ->first();

        if ($account) {
            return $account->user;
        } else {

            session([
                'callback_provider_user' => $providerUser,
                'callback_provider'      => $provider,
            ]);

            return false;
        }
    }

}