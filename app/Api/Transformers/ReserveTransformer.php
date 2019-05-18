<?php

namespace App\Api\Transformers;

use App\Api\Models\Reserve;

class ReserveTransformer extends BaseTransformer
{
    protected $availableIncludes = ['region', 'activities', 'entrances', 'passes', 'dayPass',
                                    'trails', 'trailCoverPhotos', 'trailPhotos', 'qrcodes',
                                    'contacts', 'emergencyContacts'
                                ];
    protected $defaultIncludes   = ['region'];

    public function transform(Reserve $reserve)
    {
        return [
            'id'          => (int) $reserve->ID,
            'name'        => $reserve->ReserveName,
            'summary'     => $reserve->summary,
            'description' => $reserve->description,
        ];
    }

    public function includeActivities(Reserve $reserve)
    {
        if ($activities = $reserve->activities) {
            return $this->collection($activities, new ActivityTransformer);
        }
    }

    public function includeRegion(Reserve $reserve)
    {
        if ($region = $reserve->region) {
            return $this->item($region, new RegionTransformer);
        }
    }

    public function includeEntrances(Reserve $reserve)
    {
        if ($entrances = $reserve->entrances) {
            return $this->collection($entrances, new ReserveEntranceTransformer);
        }
    }

    public function includePasses(Reserve $reserve)
    {
        if ($passes = $reserve->passes) {
            $passes = $passes->sortBy('duration.metric')->sortBy('price');

            return $this->collection($passes->keyBy('id'), new PassTransformer);
        }
    }

    public function includeDayPass(Reserve $reserve)
    {
        if ($passes = $reserve->dayPass) {
            $dayPass = $passes->where('duration.duration', 1)
                            ->where('duration.metric', 'day')
                            ->sortBy('price')
                            ->take(1);
            return $this->collection($dayPass, new PassTransformer);
        }
    }

    public function includeTrails(Reserve $reserve)
    {
        if ($trails = $reserve->trails) {
            return $this->collection($trails, new TrailTransformer);
        }
    }

    public function includeContacts(Reserve $reserve)
    {
        if ($contacts = $reserve->contacts) {
            return $this->collection($contacts, new ContactTransformer);
        }
    }

    public function includeEmergencyContacts(Reserve $reserve)
    {
        if ($emergencyContacts = $reserve->emergencyContacts) {
            return $this->collection($emergencyContacts, new ContactTransformer);
        }
    }

    public function includeTrailPhotos(Reserve $reserve)
    {
        if ($trailPhotos = $reserve->trailPhotos) {
            return $this->collection($trailPhotos, new TrailFileTransformer);
        }
    }

    public function includeTrailCoverPhotos(Reserve $reserve)
    {
        if ($trailCoverPhotos = $reserve->trailCoverPhotos) {
            return $this->collection($trailCoverPhotos, new TrailFileTransformer);
        }
    }

    public function includeQrcodes(Reserve $reserve)
    {
        if ($qrcodes = $reserve->qrcodes) {
            return $this->collection($qrcodes, new QrcodeTransformer);
        }
    }
}
