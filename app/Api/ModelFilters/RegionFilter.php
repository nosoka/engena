<?php

namespace App\Api\ModelFilters;

use EloquentFilter\ModelFilter;

class RegionFilter extends ModelFilter
{
    public function id($id)
    {
        return is_array($id) ? $this->whereIn('id', $id) : $this->whereId($id);
    }

    public function name($name)
    {
        return is_array($name) ? $this->whereIn('name', $name) : $this->whereName($name);
    }
}
