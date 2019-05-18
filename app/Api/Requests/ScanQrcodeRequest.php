<?php

namespace App\Api\Requests;

class ScanQrcodeRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [ 'hash' => 'required|exists:qrcodes,hash' ];
    }

    public function messages()
    {
        return [
            'hash.required' => 'QR Code is required',
            'hash.exists'   => 'Provided QR Code is invalid'
        ];
    }

}
