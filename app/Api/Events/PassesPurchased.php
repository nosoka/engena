<?php

namespace App\Api\Events;

use Illuminate\Support\Collection;

class PassesPurchased extends Event
{
    public $passes;

    public function __construct(Collection $passes)
    {
        $this->passes = $passes;
    }
}
