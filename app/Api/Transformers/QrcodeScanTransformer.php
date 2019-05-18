<?php

namespace App\Api\Transformers;

use App\Api\Models\Qrcode;

class QrcodeScanTransformer extends BaseTransformer
{
    protected $availableIncludes = ['reserve', 'userPasses'];
    protected $defaultIncludes   = ['reserve', 'userPasses'];

    public function transform(Qrcode $qrcode)
    {
        $status = is_null($qrcode->id) ? 'error' : 'success';
        if($qrcode->scan_type == 'checkout') {
            $this->defaultIncludes = ['reserve'];
        }

        return [ 'status' => $status ];
    }

    public function includeReserve(Qrcode $qrcode)
    {
        if ($reserve = $qrcode->reserve) {
            return $this->item($reserve->first(), new ReserveTransformer);
        }
    }

    public function includeUserPasses(Qrcode $qrcode)
    {
        // if ($qrcode->reserve) {
        if ($userPasses = $qrcode->validPasses()) {
            // $userPasses = $qrcode->validPasses();
            return $this->collection($userPasses, new UserPassTransformer);
        }
    }
}
