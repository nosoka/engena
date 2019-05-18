<?php

namespace App\Api\Models;

use Carbon\Carbon;

class UserPass extends BaseModel
{
    protected $table      = 'user_passes';
    protected $fillable   = [ 'user_id', 'pass_id', 'is_owner',
                              'pass_photo', 'pass_amount', 'start_date',
                              'end_date', 'status'
                            ];

    public function getStartDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->toFormattedDateString();
    }

    public function getEndDateAttribute($value)
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->toFormattedDateString();
    }

    public function getStatusAttribute()
    {
        $today     = Carbon::now();
        $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['start_date'])->startOfDay();
        $endDate   = Carbon::createFromFormat('Y-m-d H:i:s', $this->attributes['end_date'])->endOfDay();

        if ($startDate->gt($today) || $today->between($startDate, $endDate)) {
            return 'active';
        }

        return 'inactive';
    }

    public function pass()
    {
        return $this->belongsTo('App\Api\Models\Pass', 'pass_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\Api\Models\User', 'user_id', 'ID');
    }
}
