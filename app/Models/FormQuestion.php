<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasTranslations;

class FormQuestion extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'form_id',
        'section_id',
        'type',
        'order',
        'is_required',
        'settings',
        'conditional_logic',
    ];

    protected $casts = [
        'order' => 'integer',
        'is_required' => 'boolean',
        'settings' => 'array',
        'conditional_logic' => 'array',
    ];

    /**
     * Question types constants.
     */
    public const TYPE_SHORT_TEXT = 'short_text';
    public const TYPE_LONG_TEXT = 'long_text';
    public const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_DROPDOWN = 'dropdown';
    public const TYPE_LINEAR_SCALE = 'linear_scale';
    public const TYPE_DATE = 'date';
    public const TYPE_TIME = 'time';
    public const TYPE_DATETIME = 'datetime';
    public const TYPE_FILE_UPLOAD = 'file_upload';
    public const TYPE_EMAIL = 'email';
    public const TYPE_NUMBER = 'number';
    public const TYPE_PHONE = 'phone';
    public const TYPE_URL = 'url';
    public const TYPE_YES_NO = 'yes_no';
    public const TYPE_SECTION_BREAK = 'section_break';
    public const TYPE_PAGE_BREAK = 'page_break';

    /**
     * Get all question types.
     */
    public static function getTypes(): array
    {
        return [
            self::TYPE_SHORT_TEXT => 'Short Text',
            self::TYPE_LONG_TEXT => 'Long Text',
            self::TYPE_MULTIPLE_CHOICE => 'Multiple Choice',
            self::TYPE_CHECKBOX => 'Checkbox',
            self::TYPE_DROPDOWN => 'Dropdown',
            self::TYPE_LINEAR_SCALE => 'Linear Scale',
            self::TYPE_DATE => 'Date',
            self::TYPE_TIME => 'Time',
            self::TYPE_DATETIME => 'Date & Time',
            self::TYPE_FILE_UPLOAD => 'File Upload',
            self::TYPE_EMAIL => 'Email',
            self::TYPE_NUMBER => 'Number',
            self::TYPE_PHONE => 'Phone',
            self::TYPE_URL => 'URL',
            self::TYPE_YES_NO => 'Yes/No',
            self::TYPE_SECTION_BREAK => 'Section Break',
            self::TYPE_PAGE_BREAK => 'Page Break',
        ];
    }

    /**
     * Get the form that owns the question.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get the section that owns the question.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(FormSection::class, 'section_id');
    }

    /**
     * Get the options for the question.
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id')->orderBy('order');
    }

    /**
     * Get the answers for the question.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ResponseAnswer::class, 'question_id');
    }

    /**
     * Check if question has options.
     */
    public function hasOptions(): bool
    {
        return in_array($this->type, [
            self::TYPE_MULTIPLE_CHOICE,
            self::TYPE_CHECKBOX,
            self::TYPE_DROPDOWN,
        ]);
    }

    /**
     * Get translated question text.
     */
    public function getTranslatedQuestionText(?string $locale = null): string
    {
        return $this->getTranslated('question_text', $locale);
    }

    /**
     * Get translated help text.
     */
    public function getTranslatedHelpText(?string $locale = null): string
    {
        return $this->getTranslated('help_text', $locale);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($question) {
            // Delete associated options when question is deleted
            $question->options()->delete();
        });
    }
}
