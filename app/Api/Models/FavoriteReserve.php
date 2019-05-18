<?php

namespace App\Api\Models;

class FavoriteReserve extends BaseModel
{
    public $timestamps    = false;
    protected $table      = 'tblfavoritereserves';
    protected $primaryKey = 'ID';
    protected $fillable   = ['UserID', 'ReserveID'];

    public function reserve()
    {
        return $this->hasOne('App\Api\Models\Reserve', 'ID', 'ReserveID');
    }
}
