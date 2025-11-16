<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Traits\HasTranslations;

class Form extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'user_id',
        'slug',
        'status',
        'settings',
        'appearance',
        'allow_multiple',
        'require_auth',
        'collect_email',
        'show_progress',
        'randomize_questions',
        'expires_at',
        'starts_at',
        'redirect_url',
    ];

    protected $casts = [
        'settings' => 'array',
        'appearance' => 'array',
        'allow_multiple' => 'boolean',
        'require_auth' => 'boolean',
        'collect_email' => 'boolean',
        'show_progress' => 'boolean',
        'randomize_questions' => 'boolean',
        'expires_at' => 'datetime',
        'starts_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($form) {
            // Generate or update slug from English translation
            $titleEn = $form->getEnglishTranslation('title');
            if (!empty($titleEn)) {
                $newSlug = Str::slug($titleEn);
                if (empty($form->slug) || $newSlug !== $form->slug) {
                    // Ensure slug is unique
                    $originalSlug = $newSlug;
                    $counter = 1;
                    while (static::where('slug', $newSlug)->where('id', '!=', $form->id)->exists()) {
                        $newSlug = $originalSlug . '-' . $counter;
                        $counter++;
                    }
                    $form->slug = $newSlug;
                    $form->saveQuietly();
                }
            }
        });
    }

    /**
     * Get the user that owns the form.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the sections for the form.
     */
    public function sections(): HasMany
    {
        return $this->hasMany(FormSection::class)->orderBy('order');
    }

    /**
     * Get the questions for the form.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(FormQuestion::class)->orderBy('order');
    }

    /**
     * Get the responses for the form.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(FormResponse::class);
    }

    /**
     * Get the public form URL.
     */
    public function getPublicUrlAttribute(): string
    {
        return route('forms.show', $this->slug);
    }

    /**
     * Check if form is published and active.
     */
    public function isActive(): bool
    {
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Get translated title.
     */
    public function getTranslatedTitle(?string $locale = null): string
    {
        return $this->getTranslated('title', $locale);
    }

    /**
     * Get translated description.
     */
    public function getTranslatedDescription(?string $locale = null): string
    {
        return $this->getTranslated('description', $locale);
    }

    /**
     * Get translated thank you message.
     */
    public function getTranslatedThankYouMessage(?string $locale = null): string
    {
        return $this->getTranslated('thank_you_message', $locale);
    }

    /**
     * Get total responses count.
     */
    public function getTotalResponsesAttribute(): int
    {
        return $this->responses()->where('status', 'completed')->count();
    }
}
