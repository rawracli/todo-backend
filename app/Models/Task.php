<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags() : BelongsToMany {
        return $this->belongsToMany(Tag::class, 'tag_todo');
    }

    public function subTasks() : HasMany {
        return $this->hasMany(SubTask::class);
    }
}
