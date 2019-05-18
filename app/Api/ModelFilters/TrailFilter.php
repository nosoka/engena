<?php

namespace App\Api\ModelFilters;

use EloquentFilter\ModelFilter;

class TrailFilter extends ModelFilter
{
    public function id($id)
    {
        return is_array($id) ? $this->whereIn('ID', $id) : $this->where('ID', $id);
    }

    public function name($name)
    {
        return is_array($name) ? $this->whereIn('TrailName', $name) : $this->where('TrailName', $name);
    }

    public function activity($activity)
    {
        $this->whereHas('activity', function ($query) use ($activity) {
            return is_array($activity) ? $query->whereIn('name', $activity) : $query->whereName($activity);
        });
     }

    public function reserve($reserve)
    {
        $this->whereHas('reserve', function ($query) use ($reserve) {
            return is_array($reserve) ? $query->whereIn('ReserveName', $reserve) : $query->where('ReserveName', $reserve);
        });
    }

    public function region($region)
    {
        $this->whereHas('reserve.region', function ($query) use ($region) {
            return is_array($region) ? $query->whereIn('name', $region) : $query->whereName($region);
        });
    }
}
