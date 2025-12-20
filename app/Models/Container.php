<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Container extends Model
{
    protected $fillable = [
        'name',
        'size',
        'price',
        'weight_limit',
        'description',
        'is_available',
    ];

    protected $casts = [
        'size' => 'decimal:2',
        'price' => 'decimal:2',
        'weight_limit' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
