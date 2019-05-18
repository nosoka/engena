<?php

namespace App\Api\Models;

class UserSocialAccount extends BaseModel
{
    protected $fillable = ['user_id', 'provider', 'provider_id', 'token', 'full_name', 'avatar'];

    public function user()
    {
        return $this->hasOne('App\Api\Models\User', 'ID', 'user_id');
    }
}
