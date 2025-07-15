<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubTask extends Model
{
    protected $fillable = [
        'title',
        'description',
        'status',
        'task_id',
    ];
    public function tasks() : BelongsTo {
        return $this->belongsTo(Task::class);
    }
}
