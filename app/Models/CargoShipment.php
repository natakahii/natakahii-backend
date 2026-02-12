<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CargoShipment extends Model
{
    protected $fillable = [
        'order_id',
        'fulfillment_center_id',
        'tracking_number',
        'status',
        'destination_address',
        'recipient_name',
        'recipient_phone',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function fulfillmentCenter(): BelongsTo
    {
        return $this->belongsTo(FulfillmentCenter::class);
    }

    public function trackingEvents(): HasMany
    {
        return $this->hasMany(TrackingEvent::class);
    }

    public function deliveryRuns(): BelongsToMany
    {
        return $this->belongsToMany(DeliveryRun::class, 'delivery_run_shipments');
    }

    /**
     * Generate a unique tracking number.
     */
    public static function generateTrackingNumber(): string
    {
        return 'NTK-SHP-'.strtoupper(uniqid());
    }
}
