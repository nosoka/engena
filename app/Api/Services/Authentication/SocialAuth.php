<?php

namespace App\Api\Services\Authentication;

use App\Api\Repositories\UserRepository;
use League\OAuth2\Client\Provider\Facebook;
use League\OAuth2\Client\Provider\Google;
use League\OAuth2\Client\Provider\Strava;
use League\OAuth2\Client\Token\AccessToken;
use Tymon\JWTAuth\JWTAuth;

class SocialAuth
{
    public function __construct(JWTAuth $jwt, UserRepository $repo)
    {
        $this->jwt     = $jwt;
        $this->repo    = $repo;
        $this->request = app('request');
    }

    public function facebookLogin($accessToken = null)
    {
        if (is_null($accessToken)) {
            return false;
        }

        $provider     = $this->getFacebookProvider();
        $longToken    = $provider->getLongLivedAccessToken($accessToken);
        $providerUser = $provider->getResourceOwner($longToken);

        if ($user = $this->repo->findUserBySocialAccount('facebook', $providerUser->getId())) {
            return $this->jwt->fromUser($user);
        }
        if ($socialAccount = $this->repo->linkSocialAccount('facebook', $providerUser, $longToken)) {
            return ['message' => 'Successfully connected to your Facebook account', 'account'=> $socialAccount ];
        }
        /*
        if ($user = $this->repo->createSocialAccount('facebook', $providerUser, $longToken)) {
            return $this->jwt->fromUser($user);
        }*/

        return false;
    }

    public function googleLogin($accessToken = null)
    {
        if (is_null($accessToken)) {
            return false;
        }

        $provider     = $this->getGoogleProvider();
        $longToken    = $provider->getAccessToken('refresh_token', ['refresh_token' => $accessToken]);
        $providerUser = $provider->getResourceOwner($longToken);

        if ($user = $this->repo->findUserBySocialAccount('google', $providerUser->getId())) {
            return $this->jwt->fromUser($user);
        }
        if ($socialAccount = $this->repo->linkSocialAccount('google', $providerUser, $longToken)) {
            return ['message' => 'Successfully connected to your Google account', 'account'=> $socialAccount ];
        }
        /*
        if ($user = $this->repo->createSocialAccount('google', $providerUser, $longToken)) {
            return $this->jwt->fromUser($user);
        }*/

        return false;
    }

    public function stravaConnect($accessToken = null)
    {
        if (is_null($accessToken)) { return false; }

        $longToken    = new AccessToken(['access_token' => $accessToken]);
        $provider     = $this->getStravaProvider();
        $providerUser = $provider->getResourceOwner($longToken);

        if ($socialAccount = $this->repo->findSocialAccount('strava', $providerUser->getId())) {
            return ['message' => 'User is already connected', 'account'=> $socialAccount ];
        }
        if ($socialAccount = $this->repo->linkSocialAccount('strava', $providerUser, $longToken)) {
            return ['message' => 'Successfully connected to your strava account', 'account'=> $socialAccount ];
        }

        return false;
    }

    public function getFacebookProvider()
    {
        return new Facebook([
            'clientId'        => env('FACEBOOK_CLIENT_ID'),
            'clientSecret'    => env('FACEBOOK_CLIENT_SECRET'),
            'graphApiVersion' => env('FACEBOOK_GRAPH_VERSION'),
        ]);
    }

    public function getGoogleProvider()
    {
        return new Google([
            'clientId'     => env('GOOGLE_CLIENT_ID'),
            'clientSecret' => env('GOOGLE_CLIENT_SECRET'),
        ]);
    }

    public function getStravaProvider()
    {
        return new Strava([
            'clientId'     => env('STRAVA_CLIENT_ID'),
            'clientSecret' => env('STRAVA_CLIENT_SECRET'),
        ]);
    }

}
