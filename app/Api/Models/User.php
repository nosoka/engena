<?php

namespace App\Api\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends EloquentUser implements JWTSubject
{
    protected $table      = 'tblusers';
    protected $primaryKey = 'ID';
    protected $fillable   = ['full_name', 'Mobile', 'Username', 'Email', 'password',
                                'board_number', 'PhotoUrl', 'confirmed', 'confirmation_code' ];
    protected $hidden     = ['password'];
    protected $loginNames = ['username', 'email', 'board_number', 'mobile'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getEmailHashAttribute()
    {
        return sha1($this->attributes['Email']);
    }

    public function getWechatIdAttribute()
    {
        if(!$wechatAccount = $this->wechatAccount()->first()) {
            return false;
        }

        return $wechatAccount->provider_id;
    }

    public function linkWechatIdIfProvided()
    {
        if (!$wechatId = app('request')->header(env('WECHAT_HEADER_PARAM', ''))) {
            return false;
        }

        if($this->wechatId) {
            $this->socialAccounts()->where('provider', 'wechat')->update(['provider_id' => $wechatId]);
            return $wechatId;
        } else {
            return $this->socialAccounts()->create(['provider' => 'wechat','provider_id' => $wechatId])->provider_id;
        }
    }

    public function setPasswordAttribute($password)
    {
        //$this->attributes['password'] = app('hash')->make($password);
        $this->attributes['password'] = bcrypt($password);
    }

    public function passes()
    {
        return $this->hasMany('App\Api\Models\UserPass', 'user_id', 'ID');
    }

    public function subscriptions()
    {
        return $this->hasMany('App\Api\Models\UserSubscription', 'UserID', 'ID');
    }

    public function favoriteReserves()
    {
        return $this->belongsToMany('App\Api\Models\Reserve', 'tblfavoritereserves', 'UserID', 'ReserveID');
    }

    public function socialAccounts()
    {
        return $this->hasMany('App\Api\Models\UserSocialAccount', 'user_id', 'ID');
    }

    public function wechatAccount()
    {
        return $this->socialAccounts->where('provider', 'wechat');
    }

}
