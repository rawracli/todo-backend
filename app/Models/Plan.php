<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    public function users() : HasMany {
        return $this->hasMany(User::class);
    }
    public function orders() : HasMany {
        return $this->hasMany(Order::class);
    }
}
