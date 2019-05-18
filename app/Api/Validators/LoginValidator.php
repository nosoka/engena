<?php

namespace App\Api\Validators;

class LoginValidator extends BaseValidator
{
    protected $message = "Login failed. Please fix the errors and retry";
    protected $rules   = [
        'username' => 'required',
        'password' => 'required',
    ];
}
