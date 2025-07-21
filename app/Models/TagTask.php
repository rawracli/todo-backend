<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TagTask extends Model
{
    protected $fillable = [
        'tag_id',
        'task_id',
    ];
}
