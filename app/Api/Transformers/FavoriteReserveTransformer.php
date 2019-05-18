<?php

namespace App\Api\Transformers;

use App\Api\Models\FavoriteReserve;

class FavoriteReserveTransformer extends BaseTransformer
{
    protected $defaultIncludes = ['reserve'];

    public function transform(FavoriteReserve $favoriteReserve)
    {
        return [
            'reserveId' => (int) $favoriteReserve->ReserveID,
        ];
    }

    public function includeReserve(FavoriteReserve $favoriteReserve)
    {
        if ($reserve = $favoriteReserve->reserve) {
            return $this->item($reserve, new ReserveTransformer);
        }
    }
}
