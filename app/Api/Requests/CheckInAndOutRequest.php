<?php

namespace App\Api\Requests;

class CheckInAndOutRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [ 'qrcode' => 'required|exists:qrcodes,hash' ];
    }

    public function messages()
    {
        return [
            'qrcode.required' => 'QR Code is required',
            'qrcode.exists'   => 'Provided QR Code is invalid'
        ];
    }

}
