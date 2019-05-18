<?php

namespace App\Api\Requests;

class CreatePassesRequest extends BaseFormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'passes'             => 'required|array',
            'passes.*.id'        => 'required|numeric',
            'passes.*.quantity'  => 'required|numeric',
            // 'passes.*.startDate' => 'required',
        ];
    }
}
