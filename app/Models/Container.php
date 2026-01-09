<?php

namespace App\Models;

use App\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Container extends Model
{
    use HasTranslations;

    protected $fillable = [
        'name',
        'name_ar',
        'size',
        'price',
        'weight_limit',
        'description',
        'description_ar',
        'is_available',
    ];

    protected $appends = ['localized_name', 'localized_description'];

    protected function casts(): array
    {
        return [
            'size' => 'decimal:2',
            'price' => 'decimal:2',
            'weight_limit' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function shipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
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

    /**
     * Get the translated description based on current locale.
     */
    protected function localizedDescription(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getTranslation('description'),
        );
    }
}
