<?php

namespace App\Api\Validators;

class EmailValidator extends BaseValidator
{
    //protected $message = "No account associated with that email.";
    protected $message = "Fill in the field.";
    protected $rules   = [
        //'email' => 'required|email|exists:tblusers,Email',
        'email' => 'required'
    ];
}
