<?php

namespace App\Api\Transformers;

use App\Api\Models\Activity;

class ActivityTransformer extends BaseTransformer
{
    protected $availableIncludes = ['reserves', 'trails'];

    public function transform(Activity $activity)
    {
        return [
            'id'   => (int) $activity->id,
            'name' => $activity->name,
        ];
    }

    public function includeReserves(Activity $activity)
    {
        if ($reserves = $activity->reserves) {
            return $this->collection($reserves, new ReserveTransformer);
        }
    }

    public function includeTrails(Activity $activity)
    {
        if ($trails = $activity->trails) {
            return $this->collection($trails, new TrailTransformer);
        }
    }
}
