<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class Route extends Model
{
    protected $fillable = [
        'origin_port_id',
        'destination_port_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $with = ['originPort', 'destinationPort'];

    public function originPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'origin_port_id');
    }

    public function destinationPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'destination_port_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }

    public function getRouteNameAttribute(): string
    {
        return "{$this->originPort->name} → {$this->destinationPort->name}";
    }

    public function toArray(): array
    {
        $array = parent::toArray();

        // Explicitly include relationships in JSON
        if ($this->relationLoaded('originPort')) {
            $array['originPort'] = $this->originPort;
        }
        if ($this->relationLoaded('destinationPort')) {
            $array['destinationPort'] = $this->destinationPort;
        }

        return $array;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Route $route): void {
            $validator = Validator::make([
                'origin_port_id' => $route->origin_port_id,
                'destination_port_id' => $route->destination_port_id,
            ], [
                'destination_port_id' => [
                    'required',
                    'different:origin_port_id',
                ],
            ], [
                'destination_port_id.different' => 'The origin port and destination port must be different.',
            ]);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
        });
    }
}
