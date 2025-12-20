<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Port extends Model
{
    protected $fillable = [
        'name',
        'code',
        'latitude',
        'longitude',
        'country',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
        ];
    }

    public function originRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'origin_port_id');
    }

    public function destinationRoutes(): HasMany
    {
        return $this->hasMany(Route::class, 'destination_port_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
