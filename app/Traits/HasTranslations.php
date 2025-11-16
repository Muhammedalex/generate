<?php

namespace App\Traits;

trait HasTranslations
{
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
        $translationsField = $field . '_translations';
        
        // If translations column exists and has data
        if (isset($this->attributes[$translationsField]) && $this->attributes[$translationsField]) {
            $translations = is_string($this->attributes[$translationsField]) 
                ? json_decode($this->attributes[$translationsField], true) 
                : $this->attributes[$translationsField];
            
            if (is_array($translations) && isset($translations[$locale])) {
                return $translations[$locale];
            }
            
            // Fallback to English if current locale not found
            if (is_array($translations) && isset($translations['en'])) {
                return $translations['en'];
            }
        }
        
        // Fallback to original field value
        return $this->attributes[$field] ?? '';
    }

    /**
     * Set translated value for a field.
     *
     * @param string $field
     * @param array $translations
     * @return void
     */
    public function setTranslated(string $field, array $translations): void
    {
        $translationsField = $field . '_translations';
        $this->attributes[$translationsField] = json_encode($translations);
    }
}

