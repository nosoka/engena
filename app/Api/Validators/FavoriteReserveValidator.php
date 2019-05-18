<?php

namespace App\Api\Validators;

class FavoriteReserveValidator extends BaseValidator
{
    protected $rules = [
        'reserveId' => 'required|exists:tblreserves,ID',
    ];

    public function message()
    {
        if (app('request')->isMethod('post')) {
            return "Unable to add the reserve to your favorites. Please retry or contact support";
        }

        if (app('request')->isMethod('delete')) {
            return "Unable to delete the reserve from your favorites. Please retry or contact support";
        }
    }
}
