<?php

namespace App\Api\Models;

class Region extends BaseModel
{
    public function reserves()
    {
        return $this->hasMany('App\Api\Models\Reserve');
    }
}
