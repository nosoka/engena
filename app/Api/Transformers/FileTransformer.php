<?php

namespace App\Api\Transformers;

use App\Api\Models\File;

class FileTransformer extends BaseTransformer
{
    public function transform(File $file)
    {
        $row = [
            'id'          => (int) $file->id,
            'name'        => $file->name,
            'title'       => $file->title,
            'mime_type'   => $file->mime_type,
            'description' => $file->description,
            'url'         => $file->url,
        ];

        if (preg_match('/^image/', $file->mime_type)) {
            $row['thumbs'] = $this->fileService()->guessThumbImageUrls($file->url);
        }

        return $row;
    }
}
