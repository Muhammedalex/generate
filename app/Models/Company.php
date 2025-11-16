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
        'name',
        'slug',
        'description',
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
        'name_translations',
        'description_translations',
    ];

    protected $casts = [
        'social_links' => 'array',
        'name_translations' => 'array',
        'description_translations' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->slug)) {
                $company->slug = Str::slug($company->name);
            }
        });

        static::updating(function ($company) {
            if ($company->isDirty('name') && empty($company->slug)) {
                $company->slug = Str::slug($company->name);
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
        return $this->getTranslated('name', $locale) ?: $this->attributes['name'] ?? '';
    }

    /**
     * Get translated description.
     */
    public function getTranslatedDescription(?string $locale = null): string
    {
        return $this->getTranslated('description', $locale) ?: $this->attributes['description'] ?? '';
    }
}
