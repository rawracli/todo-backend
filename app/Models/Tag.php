<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    public function tasks() : BelongsToMany {
        return $this->belongsToMany(Task::class, table: 'tag_todo');
    }
}
