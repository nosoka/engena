<?php

namespace App\Api\Transformers;

use App\Api\Models\Qrcode;

class QrcodeTransformer extends BaseTransformer
{
    public function transform(Qrcode $qrcode)
    {
        $row = [
            'id'          => (int) $qrcode->id,
            'data'        => $qrcode->data,
            'description' => $qrcode->description,
            'hash'        => $qrcode->hash,
            'image_url'   => $qrcode->image_url,
            'thumbs'      => $this->fileService()->guessThumbImageUrls($qrcode->image_url)
        ];

        return $row;
    }
}
