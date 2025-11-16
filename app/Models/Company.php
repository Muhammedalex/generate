<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\HasTranslations;

class Company extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'user_id',
        'slug',
        'email',
        'phone',
        'website',
        'address',
        'primary_color',
        'secondary_color',
        'accent_color',
        'logo_path',
        'social_links',
        'is_active',
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($company) {
            // Generate or update slug from English translation
            $nameEn = $company->getEnglishTranslation('name');
            if (!empty($nameEn)) {
                $newSlug = Str::slug($nameEn);
                if (empty($company->slug) || $newSlug !== $company->slug) {
                    // Ensure slug is unique
                    $originalSlug = $newSlug;
                    $counter = 1;
                    while (static::where('slug', $newSlug)->where('id', '!=', $company->id)->exists()) {
                        $newSlug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                    $company->slug = $newSlug;
                    $company->saveQuietly();
                }
            }
        });
    }

    /**
     * Get the user that owns the company.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the public profile URL.
     */
    public function getPublicUrlAttribute()
    {
        return route('company.show', $this->slug);
    }

    /**
     * Get translated name.
     */
    public function getTranslatedName(?string $locale = null): string
    {
        return $this->getTranslated('name', $locale);
    }

    /**
     * Get translated description.
     */
    public function getTranslatedDescription(?string $locale = null): string
    {
        return $this->getTranslated('description', $locale);
    }
}
