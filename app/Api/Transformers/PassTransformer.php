<?php

namespace App\Api\Transformers;

use App\Api\Models\Pass;

class PassTransformer extends BaseTransformer
{
    protected $availableIncludes = ['reserve'];
    protected $defaultIncludes   = ['reserve'];

    public function transform(Pass $pass)
    {
        return [
            'id'              => $pass->id,
            'name'            => $pass->name,
            'description'     => $pass->description,
            'price'           => round($pass->price,2),
            'duration'        => $pass->duration->duration,
            'duration_metric' => $pass->duration->metric,
        ];
    }

    public function includeReserve(Pass $pass)
    {
        if($reserve = $pass->reserve) {
            return $this->item($reserve, new ReserveTransformer);
        }
    }
}
