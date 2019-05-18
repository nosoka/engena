<?php

namespace App\Api\Transformers;

use App\Api\Models\TrailFile;

class TrailFileTransformer extends BaseTransformer
{
    protected $availableIncludes = ['file'];
    protected $defaultIncludes   = ['file'];

    public function transform(TrailFile $trailFile)
    {
        return [
        'id'         => (int) $trailFile->id,
        'is_primary' => (int) $trailFile->is_primary
        ];
    }

    public function includeFile(TrailFile $trailFile)
    {
        if ($file = $trailFile->file) {
            return $this->item($file, new FileTransformer);
        }
    }

}
