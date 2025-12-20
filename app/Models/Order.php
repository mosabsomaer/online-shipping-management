<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'merchant_id',
        'route_id',
        'tracking_number',
        'recipient_name',
        'recipient_phone',
        'recipient_address',
        'customs_fee',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'duration_days' => 'integer',
        'customs_fee' => 'decimal:2',
    ];

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    public function route(): BelongsTo
    {
        return $this->belongsTo(Route::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Calculate total cost from all shipments
     */
    public function getTotalCostAttribute(): float
    {
        return $this->shipments->sum(function ($shipment) {
            return $shipment->container_price + $shipment->customs_fee;
        });
    }
}
