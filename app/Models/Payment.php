<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'paid_at',
        'transaction_status',
        'snap_token'
    ];
   public function orders() : BelongsTo {
    return $this->belongsTo(Order::class);
   }
}
