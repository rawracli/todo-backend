<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'amount',
        'status'
    ];
    public function users() : BelongsTo {
        return $this->belongsTo(User::class);
    }
    public function plans() : BelongsTo {
        return $this->belongsTo(Plan::class);
    }
    public function invoice() : HasOne {
        return $this->hasOne(Invoice::class);
    }
    public function payment() : HasOne {
        return $this->hasOne(Payment::class);
    }
}
