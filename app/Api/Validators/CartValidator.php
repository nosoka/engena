<?php

namespace App\Api\Validators;

class CartValidator extends BaseValidator
{
    protected $message = "Unable to process your payment. Please contact support.";
    protected $rules   = ['amount' => 'required|numeric'];
}
