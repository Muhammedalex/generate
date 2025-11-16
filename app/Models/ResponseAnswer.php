<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ResponseAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'response_id',
        'question_id',
        'answer_text',
        'answer_number',
        'answer_boolean',
        'answer_date',
        'answer_json',
        'file_path',
    ];

    protected $casts = [
        'answer_number' => 'decimal:4',
        'answer_boolean' => 'boolean',
        'answer_date' => 'date',
        'answer_json' => 'array',
    ];

    /**
     * Get the response that owns the answer.
     */
    public function response(): BelongsTo
    {
        return $this->belongsTo(FormResponse::class, 'response_id');
    }

    /**
     * Get the question that this answer belongs to.
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(FormQuestion::class, 'question_id');
    }

    /**
     * Get the answer value based on question type.
     */
    public function getValue()
    {
        if ($this->answer_text !== null) {
            return $this->answer_text;
        }

        if ($this->answer_number !== null) {
            return $this->answer_number;
        }

        if ($this->answer_boolean !== null) {
            return $this->answer_boolean;
        }

        if ($this->answer_date !== null) {
            return $this->answer_date;
        }

        if ($this->answer_json !== null) {
            return $this->answer_json;
        }

        if ($this->file_path !== null) {
            return $this->file_path;
        }

        return null;
    }
}
