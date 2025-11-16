<?php

namespace App\Traits;

use App\Models\Translation;

trait HasTranslations
{
    /**
     * Boot the trait.
     */
    protected static function bootHasTranslations()
    {
        // Delete translations when model is deleted
        static::deleting(function ($model) {
            $model->translations()->delete();
        });
    }

    /**
     * Get the translations relationship.
     */
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Get translated value for a field.
     *
     * @param string $field
     * @param string|null $locale
     * @return string
     */
    public function getTranslated(string $field, ?string $locale = null): string
    {
        $locale = $locale ?? app()->getLocale();
        
        // Try to get translation from database
        $translation = $this->translations()
            ->where('locale', $locale)
            ->where('field_name', $field)
            ->first();
        
        if ($translation) {
            return $translation->value;
        }
        
        // Fallback to English if current locale not found
        if ($locale !== 'en') {
            $translation = $this->translations()
                ->where('locale', 'en')
                ->where('field_name', $field)
                ->first();
            
            if ($translation) {
                return $translation->value;
            }
        }
        
        return '';
    }

    /**
     * Set translated value for a field.
     *
     * @param string $field
     * @param string $locale
     * @param string $value
     * @return void
     */
    public function setTranslated(string $field, string $locale, string $value): void
    {
        $this->translations()->updateOrCreate(
            [
                'locale' => $locale,
                'field_name' => $field,
            ],
            [
                'value' => $value,
            ]
        );
    }

    /**
     * Set multiple translations for a field.
     *
     * @param string $field
     * @param array $translations ['en' => 'value', 'ar' => 'value']
     * @return void
     */
    public function setTranslations(string $field, array $translations): void
    {
        foreach ($translations as $locale => $value) {
            if (!empty($value)) {
                $this->setTranslated($field, $locale, $value);
            }
        }
    }

    /**
     * Get all translations for a field.
     *
     * @param string $field
     * @return array
     */
    public function getTranslations(string $field): array
    {
        return $this->translations()
            ->where('field_name', $field)
            ->pluck('value', 'locale')
            ->toArray();
    }

    /**
     * Get English translation (required for slug generation).
     *
     * @param string $field
     * @return string
     */
    public function getEnglishTranslation(string $field): string
    {
        return $this->getTranslated($field, 'en');
    }
}
