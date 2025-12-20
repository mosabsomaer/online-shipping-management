<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    protected $fillable = [
        'order_id',
        'container_id',
        'item_description',
        'container_price',
        'customs_fee',
        'current_status',
        'last_updated',
        'last_synced_at',
        'cached_status',
    ];

    protected $casts = [
        'container_price' => 'decimal:2',
        'customs_fee' => 'decimal:2',
        'last_updated' => 'datetime',
        'last_synced_at' => 'datetime',
        'cached_status' => 'array',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(ShipmentStatusHistory::class)->orderBy('created_at', 'desc');
    }

    /**
     * Calculate total cost for this shipment
     */
    public function getTotalCostAttribute(): float
    {
        return $this->container_price + $this->customs_fee;
    }

    /**
     * Check if shipment needs tracking sync
     */
    public function needsSync(): bool
    {
        if (! $this->last_synced_at) {
            return true;
        }

        return $this->last_synced_at->lt(now()->subHour());
    }
}
