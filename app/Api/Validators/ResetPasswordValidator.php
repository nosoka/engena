<?php

namespace App\Api\Validators;

class ResetPasswordValidator extends BaseValidator
{
    protected $message = "Invalid reset code";
    protected $rules   = [
        'code'     => 'required|min:64',
        'password' => 'required|min:3',
    ];
}
