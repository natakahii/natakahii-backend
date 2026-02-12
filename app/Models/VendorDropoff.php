<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorDropoff extends Model
{
    protected $fillable = [
        'vendor_id',
        'order_id',
        'fulfillment_center_id',
        'status',
        'notes',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function fulfillmentCenter(): BelongsTo
    {
        return $this->belongsTo(FulfillmentCenter::class);
    }
}
