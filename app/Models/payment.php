<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class payment extends Model
{
    protected $fillable = [
        'order_id',
        'payment_intent_id',
        'amount',
        'currency',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(order::class);
    }
}
