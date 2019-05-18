<?php

namespace App\Api\Requests;

class CreateUserRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [
            'wechatId.unique' => 'Provided WeChat-ID is already registered with us',
        ];
    }

    protected function getValidatorInstance()
    {
        $this->merge([
            'wechatId'=> app('request')->header(env('WECHAT_HEADER_PARAM', '')),
        ]);

        return parent::getValidatorInstance();
    }

    public function rules()
    {
        return [
            'username'  => 'required|max:45|unique:tblusers',
            'password'  => 'required|min:3',
            'fullname'  => 'required|max:255',
            'mobile'    => 'required|max:45|unique:tblusers',
            'email'     => 'required|email|max:255|unique:tblusers',
            'wechatId'  => "unique:user_social_accounts,provider_id,NULL,id,provider,wechat",
        ];
    }
}
