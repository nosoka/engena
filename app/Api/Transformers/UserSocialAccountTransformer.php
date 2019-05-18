<?php

namespace App\Api\Transformers;

use App\Api\Models\UserSocialAccount;

class UserSocialAccountTransformer extends BaseTransformer
{
    public function transform($socialAccount)
    {
        if (is_null($socialAccount)) { return []; }

        $socialAccount = [
            'provider'    => $socialAccount->provider,
            'provider_id' => $socialAccount->provider_id,
            'token'       => $socialAccount->token,
            'full_name'   => $socialAccount->full_name,
            'avatar'      => $socialAccount->avatar,
        ];
        if($socialAccount['provider'] == 'strava') {
            $socialAccount['profile'] = "http://strava.com/athletes/{$socialAccount['provider_id']}";
        }

        return $socialAccount;
    }
}
