<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasTranslations;

class QuestionOption extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'question_id',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    /**
     * Get the question that owns the option.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(FormQuestion::class, 'question_id');
    }

    /**
     * Get translated option text.
     */
    public function getTranslatedOptionText(?string $locale = null): string
    {
        return $this->getTranslated('option_text', $locale);
    }
}
