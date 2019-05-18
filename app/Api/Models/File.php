<?php

namespace App\Api\Models;

class File extends BaseModel
{
    protected $table    = 'files';
    protected $fillable = ['name', 'title', 'description', 'mime_type', 'real_path', 'url'];
}
