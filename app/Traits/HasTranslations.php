<?php

namespace App\Traits;

use Illuminate\Support\Facades\App;

trait HasTranslations
{
    /**
     * Get the translated value for an attribute.
     * For Arabic locale, returns {attribute}_ar if available.
     * Falls back to the base attribute (English) otherwise.
     */
    public function getTranslation(string $attribute, ?string $locale = null): ?string
    {
        $locale = $locale ?? App::getLocale();

        if ($locale === 'ar') {
            $arabicAttribute = "{$attribute}_ar";

            return $this->{$arabicAttribute} ?? $this->{$attribute} ?? null;
        }

        return $this->{$attribute} ?? null;
    }
}
