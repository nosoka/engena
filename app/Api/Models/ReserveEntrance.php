<?php

namespace App\Api\Models;

class ReserveEntrance extends BaseModel
{
    protected $table      = 'tblreserveentrances';
    protected $primaryKey = 'ID';

    public function reserve()
    {
        return $this->belongsTo('App\Api\Models\Reserve', 'ReserveID', 'ID');
    }

    public function activities()
    {
        return $this->belongsToMany('App\Api\Models\Activity', 'tblreserveactivities', 'ReserveID', 'activity_id');
    }
}
