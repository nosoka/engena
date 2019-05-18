<?php

namespace App\Api\Repositories;

use App\Api\Models\Trail;
use App\Api\Models\File;
use Symfony\Component\HttpFoundation\File\File as SplFileInfo;

class TrailRepository extends BaseRepository
{
    public $realDir;
    public $urlDir;

    public function __construct(Trail $model, File $fileModel)
    {
        $this->fileModel = $fileModel;
        $this->model     = $model;
        $this->urlDir    = url('/api/public/images/trails/');
        $this->realDir   = base_path('/public/images/trails/');
    }

    public function addFile(SplFileInfo $splFile)
    {
        $data  = [
            'name'      => $splFile->getFilename(),
            'mime_type' => $splFile->getMimeType(),
            'real_path' => $splFile->getRealPath(),
            'url'       => "{$this->urlDir}/{$splFile->getFilename()}",
        ];

        return $this->fileModel->create($data);
    }
}
