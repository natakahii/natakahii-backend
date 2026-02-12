<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FulfillmentCenter extends Model
{
    protected $fillable = [
        'name',
        'code',
        'address',
        'city',
        'region',
        'latitude',
        'longitude',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function dropoffs(): HasMany
    {
        return $this->hasMany(VendorDropoff::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(CargoShipment::class);
    }
}
