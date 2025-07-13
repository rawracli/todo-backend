<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    public function users() : BelongsToMany {
        return $this->belongsToMany(User::class);
    }
    public function plans() : BelongsToMany {
        return $this->belongsToMany(Plan::class);
    }
    public function invoices() : HasMany {
        return $this->hasMany(Invoice::class);
    }
    public function payments() : HasMany {
        return $this->hasMany(Payment::class);
    }
}
