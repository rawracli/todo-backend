<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SubTask extends Model
{
    public function tasks() : BelongsToMany {
        return $this->belongsToMany(Task::class);
    }
}
