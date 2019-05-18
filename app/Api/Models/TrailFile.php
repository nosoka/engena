<?php

namespace App\Api\Models;

class TrailFile extends BaseModel
{
    protected $table = 'trail_files';

    public function file()
    {
        return $this->belongsTo('App\Api\Models\File');
    }
}
