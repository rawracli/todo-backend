<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'title',
        'user_id'
    ];
    public function tasks() : BelongsToMany {
        return $this->belongsToMany(Task::class, 'tag_tasks');
    }
}
