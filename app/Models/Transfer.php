<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $fillable = [
        'transfer_id',
        'supplier_id',
        'order_id',
        'payment_intent_id',
        'amount',
        'currency',
        'stripe_transfer_id',
        'status',
        'raw_response',
    ];

    protected  $casts = [
        'raw_response' => 'array',
  ];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
