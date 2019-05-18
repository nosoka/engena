<?php

namespace App\Api\Transformers;

use App\Api\Services\FileService;
use League\Fractal\TransformerAbstract;

class BaseTransformer extends TransformerAbstract
{
    public function fileService()
    {
        return new FileService;
    }
}
