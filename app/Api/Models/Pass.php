<?php

namespace App\Api\Models;

class Pass extends BaseModel {

    protected $table      = 'passes';

    public function duration() {
        return $this->belongsTo('App\Api\Models\PassDuration');
    }

    public function reserve() {
        return $this->belongsTo('App\Api\Models\Reserve', 'reserve_id', 'ID');
    }
}
