<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTranslations;

class FormSection extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'form_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the form that owns the section.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get the questions for the section.
     */
    public function questions(): HasMany
    {
        return $this->hasMany(FormQuestion::class, 'section_id')->orderBy('order');
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
}
