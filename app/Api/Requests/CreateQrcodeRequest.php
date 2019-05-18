<?php

namespace App\Api\Requests;

class CreateQrcodeRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [ 'data' => 'required' ];
    }
}
