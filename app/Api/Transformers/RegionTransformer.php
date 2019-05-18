<?php

namespace App\Api\Transformers;

use App\Api\Models\Region;

class RegionTransformer extends BaseTransformer
{
    protected $availableIncludes = ['reserves'];

    public function transform(Region $region)
    {
        return [
            'id'   => (int) $region->id,
            'name' => $region->name,
        ];
    }

    public function includeReserves(Region $region)
    {
        if ($reserves = $region->reserves) {
            return $this->collection($reserves, new ReserveTransformer);
        }
    }
}
