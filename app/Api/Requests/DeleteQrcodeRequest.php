<?php

namespace App\Api\Requests;

class DeleteQrcodeRequest extends BaseFormRequest
{
    public function validationData()
    {
        return ['id' => $this->route('id')];
    }

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [ 'id' => 'required|exists:qrcodes,id' ];
    }
}
