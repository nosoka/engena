<?php

namespace App\Api\Models;

class Reserve extends BaseModel
{
    protected $table      = 'tblreserves';
    protected $primaryKey = 'ID';
    public $timestamps    = false;

    public function getSummaryAttribute()
    {
        $summary = html_entity_decode(strip_tags($this->attributes['description']));
        $summary = wordwrap($summary, 250);
        $summary = explode("\n", $summary)[0];

        return $summary;
    }

    public function region()
    {
        return $this->belongsTo('App\Api\Models\Region');
    }

    public function activities()
    {
        return $this->belongsToMany('App\Api\Models\Activity', 'tblreserveactivities', 'ReserveID', 'activity_id');
    }

    public function trails()
    {
        return $this->hasMany('App\Api\Models\Trail', 'ReserveID', 'ID')->with(['photos','coverPhoto','reserve', 'activity'])
        ->whereEnabled(1);
    }

    public function contacts()
    {
        return $this->hasMany('App\Api\Models\ReserveContact', 'reserve_id', 'ID')->whereEnabled(1);
    }

    public function emergencyContacts()
    {
        return $this->contacts()->whereEmergency(1);
    }

    public function qrcodes()
    {
        return $this->belongsToMany('App\Api\Models\Qrcode', 'reserve_qrcodes', 'reserve_id', 'qrcode_id');
    }

    public function entrances()
    {
        return $this->hasMany('App\Api\Models\ReserveEntrance', 'ReserveID', 'ID');
    }

    public function userPasses()
    {
        return $this->hasManyThrough('App\Api\Models\UserPass', 'App\Api\Models\Pass', 'reserve_id', 'pass_id', 'ID');
    }

    public function trailFiles()
    {
        return $this->hasManyThrough('App\Api\Models\TrailFile', 'App\Api\Models\Trail','ReserveID', 'trail_id', 'ID');
    }

    public function trailPhotos()
    {
        return $this->trailFiles()
                    ->with(['file' => function ($query) { $query->where('mime_type', 'like', 'image/%'); }])
                    ->orderBy('is_primary', 'desc')
                    ->orderBy('id', 'asc');
    }

    public function trailCoverPhotos()
    {
        return $this->trailPhotos()->whereIsPrimary(1);
    }

    public function passes()
    {
        return $this->hasMany('App\Api\Models\Pass', 'reserve_id', 'ID')->with(['duration']);
    }

    public function dayPass()
    {
        // apply the constraint in transformer method over the collection
        return $this->passes();
    }

    public function monthlyPass()
    {
        // apply the constraint in transformer method over the collection
        return $this->passes();
    }

    public function validUserPasses()
    {
        $date    = date('Y-m-d');
        $userId  = app('api.auth')->user()->ID;

        return $this->userPasses()
            ->whereUserId($userId)
            ->where('start_date', '<=', $date)
            ->where('end_date', '>=', $date)
            // ->whereIsOwner(true)
            ->get();
    }
}
