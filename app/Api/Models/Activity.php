<?php

namespace App\Api\Models;

class Activity extends BaseModel
{
    protected $fillable = [ 'name', 'description' ];

    public function reserves()
    {
        return $this->belongsToMany('App\Api\Models\Reserve', 'tblreserveactivities', 'activity_id', 'ReserveID');
    }

    public function trails()
    {
        return $this->hasMany('App\Api\Models\Trail')
                    ->with(['photos','coverPhoto','reserve', 'activity'])
                    ->whereEnabled(1);
    }
}
