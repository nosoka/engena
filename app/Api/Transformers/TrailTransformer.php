<?php

namespace App\Api\Transformers;

use App\Api\Models\Trail;

class TrailTransformer extends BaseTransformer
{
    protected $availableIncludes = ['reserve', 'activity', 'files', 'photos', 'coverPhoto'];
    protected $defaultIncludes   = ['reserve', 'activity', 'photos', 'coverPhoto'];

    public function transform(Trail $trail)
    {
        return [
            'id'            => (int) $trail->ID,
            'name'          => $trail->TrailName,
            'summary'       => $trail->summary,
            'description'   => $trail->TrailDescription,
            'googlemapsUrl' => $trail->TrailMapURL,
        ];
    }

    public function includeReserve(Trail $trail)
    {
        if ($reserve = $trail->reserve) {
            return $this->item($reserve, new ReserveTransformer);
        }
    }

    public function includeActivity(Trail $trail)
    {
        if ($activity = $trail->activity) {
            return $this->item($activity, new ActivityTransformer);
        }
    }

    public function includeFiles(Trail $trail)
    {
        if ($files = $trail->files) {
            return $this->collection($files, new FileTransformer);
        }
    }

    public function includePhotos(Trail $trail)
    {
        if ($photos = $trail->photos) {
            return $this->collection($photos, new FileTransformer);
        }
    }

    public function includeCoverPhoto(Trail $trail)
    {
        if ($coverPhoto = $trail->photos->first()) {
            return $this->item($coverPhoto, new FileTransformer);
        }
    }
}
