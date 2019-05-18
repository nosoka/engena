<?php

namespace App\Api\Transformers;

use App\Api\Models\ReserveEntrance;

class ReserveEntranceTransformer extends BaseTransformer
{
    protected $availableIncludes = ['activities', 'reserve'];

    protected $defaultIncludes = [];

    public function transform(ReserveEntrance $entrance)
    {
        $entrance = [
            'entranceId'        => (int) $entrance->ID,
            'entranceName'      => $entrance->EntranceName,
            'entranceLongitude' => $entrance->EntranceLongitude,
            'entranceLatitude'  => $entrance->EntranceLatitude,
            'entrancePoint'     => $entrance->EntrancePoint,
        ];

        return $entrance;
    }

    public function includeReserve(ReserveEntrance $entrance)
    {
        if ($reserve = $entrance->reserve) {
            return $this->item($reserve, new ReserveTransformer);
        }
    }

    public function includeActivities(ReserveEntrance $entrance)
    {
        if ($activities = $entrance->activities) {
            return $this->collection($activities, new ActivityTransformer);
        }
    }
}
