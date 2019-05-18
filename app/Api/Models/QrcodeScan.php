<?php

namespace App\Api\Models;

class QrcodeScan extends BaseModel
{
    protected $table    = 'qrcode_scans';
    protected $fillable = ['user_id', 'qrcode_id', 'scan_type', 'source', 'gps'];

    public function add(array $data = array())
    {
        $data  = collect($data)->only($this->fillable)->toArray();

        return $this->create($data);
    }
}
