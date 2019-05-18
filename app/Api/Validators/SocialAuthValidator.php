<?php

namespace App\Api\Validators;

class SocialAuthValidator extends BaseValidator
{
    protected $message = "Login failed. Please fix the errors and retry";
    protected $rules   = [
        'accessToken' => 'required',
        // 'provider' => 'required'
    ];
}
