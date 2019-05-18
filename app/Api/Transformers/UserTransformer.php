<?php

namespace App\Api\Transformers;

use App\Api\Models\User;

class UserTransformer extends BaseTransformer
{
    protected $availableIncludes = ['passes', 'subscriptions', 'favorites', 'socialAccounts', 'strava'];

    public function transform(User $user)
    {
        return [
            'fullName'  => $user->full_name,
            'mobile'    => $user->Mobile,
            'email'     => $user->Email,
            'photoUrl'  => $user->PhotoUrl,
            'photos'    => $this->fileService()->guessThumbImageUrls($user->PhotoUrl),
        ];
    }

    public function includePasses(User $user)
    {
        if ($passes = $user->passes) {
            return $this->collection($passes, new UserPassTransformer);
        }
    }

    public function includeSubscriptions(User $user)
    {
        if ($subscriptions = $user->subscriptions) {
            return $this->collection($subscriptions, new UserSubscriptionTransformer);
        }
    }

    public function includeFavorites(User $user)
    {
        if ($favoriteReserves = $user->favoriteReserves) {
            return $this->collection($favoriteReserves, new ReserveTransformer);
        }
    }

    public function includeSocialAccounts(User $user)
    {
        if ($socialAccounts = $user->socialAccounts) {
            return $this->collection($socialAccounts, new UserSocialAccountTransformer);
        }
    }

    public function includeStrava(User $user)
    {
        if ($socialAccounts = $user->socialAccounts) {
            $socialAccounts = $socialAccounts->where('provider', 'strava')->first();
            return $this->item($socialAccounts, new UserSocialAccountTransformer);
        }
    }

}
