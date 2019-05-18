<?php

namespace App\Api\Models;

class Qrcode extends BaseModel
{
    protected $table    = 'qrcodes';
    protected $fillable = ['data', 'description', 'hash', 'image_path', 'image_url'];

    public function findByHash($value='')
    {
        return $this->whereHash($value)->first();
    }

    public function reserve()
    {
        return $this->belongsToMany('App\Api\Models\Reserve', 'reserve_qrcodes', 'qrcode_id', 'reserve_id', 'ID');
    }

    // TODO:: dummy method to fool fractal eager loading
    public function userPasses()
    {
        return $this->reserve();
    }

    public function validPasses()
    {
        $reserve = $this->reserve()->first();
        return $reserve->validUserPasses();
    }
}
