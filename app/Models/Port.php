<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Port extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'name_ar',
        'code',
        'latitude',
        'longitude',
        'country',
        'is_active',
    ];

    protected $appends = ['localized_name'];

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

    /**
     * Get the translated name based on current locale.
     */
    protected function localizedName(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getTranslation('name'),
        );
    }
}
