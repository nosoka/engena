<?php

namespace App\Api\Models;

use Cartalyst\Sentinel\Reminders\EloquentReminder;

class UserReminder extends EloquentReminder
{
    protected $table = 'user_reminders';
}
