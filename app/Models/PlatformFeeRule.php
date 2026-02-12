<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformFeeRule extends Model
{
    protected $fillable = [
        'name',
        'type',
        'value',
        'applies_to',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}
