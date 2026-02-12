<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DeliveryRun extends Model
{
    protected $fillable = [
        'assigned_to',
        'fulfillment_center_id',
        'status',
        'scheduled_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function fulfillmentCenter(): BelongsTo
    {
        return $this->belongsTo(FulfillmentCenter::class);
    }

    public function shipments(): BelongsToMany
    {
        return $this->belongsToMany(CargoShipment::class, 'delivery_run_shipments');
    }
}
