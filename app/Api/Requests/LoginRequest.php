<?php

namespace App\Api\Requests;

use App\Api\Repositories\UserRepository;

class LoginRequest extends BaseFormRequest
{

    public function __construct(UserRepository $repo)
    {
        $this->repo = $repo;
        parent::__construct();
    }

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
        $userId = '';
        if ($user = $this->repo->findUserByLogin($this->all()['username'])) {
            $userId = $user->ID;
        }

        return [
            'username' => 'required',
            'password' => 'required',
            'wechatId' => "unique:user_social_accounts,provider_id,{$userId},user_id,provider,wechat",
        ];
    }
}
