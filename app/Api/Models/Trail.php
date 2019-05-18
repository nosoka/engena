<?php

namespace App\Api\Models;

class Trail extends BaseModel
{
    protected $table      = 'tbltrails';
    protected $primaryKey = 'ID';

    public function getSummaryAttribute()
    {
        $summary = html_entity_decode(strip_tags($this->attributes['TrailDescription']));
        $summary = wordwrap($summary, 150);
        $summary = explode("\n", $summary)[0];

        return $summary;
    }

    public function reserve()
    {
        return $this->belongsTo('App\Api\Models\Reserve', 'ReserveID', 'ID');
    }

    public function activity()
    {
        return $this->belongsTo('App\Api\Models\Activity');
    }

    public function files()
    {
        return $this->belongsToMany('App\Api\Models\File', 'trail_files', 'trail_id', 'file_id');
    }

    public function photos()
    {
        return $this->files()->where('mime_type', 'like', 'image/%')
                    ->orderBy('is_primary', 'desc')
                    ->orderBy('id', 'asc');
    }

    public function coverPhoto()
    {
        return $this->photos()->whereIsPrimary(1);
    }

    public function scopeActive($query)
    {
        return $query->where('enabled', 1)
                    ->whereHas('reserve', function ($query){
                        return $query->where('enabled', 1);
                    });
    }

}
