<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'order_id',
        'invoice_number',
        'pdf_url'
    ];
    public function order() : BelongsTo {
        return $this->belongsTo(Order::class);
    }
}
