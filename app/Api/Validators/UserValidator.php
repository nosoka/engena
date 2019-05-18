<?php

namespace App\Api\Validators;

class UserValidator extends BaseValidator
{
    public function rules()
    {
        if (app('request')->isMethod('put')) {
            return [
                'fullname'  => 'required|max:255',
                'mobile'    => 'required|max:45|unique:tblusers,mobile,' . $this->auth->user()->ID,
                'email'     => 'required|email|max:255|unique:tblusers,email,' . $this->auth->user()->ID,
                'password' => 'max:45|min:3',
                'confirm' => 'max:45|min:3|same:password',
            ];
        }

        if (app('request')->isMethod('post')) {
            return ['filename' => 'required|image'];
        }
    }

    public function message()
    {
        if (app('request')->isMethod('put')) {
            return "Unable to update user. Please fix the errors and retry.";
        }

        if (app('request')->isMethod('post')) {
            return "Unable to upload user photo. Please fix the errors and retry.";
        }
    }
}
