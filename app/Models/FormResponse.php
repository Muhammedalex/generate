<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FormResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'form_id',
        'user_id',
        'email',
        'ip_address',
        'user_agent',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * Status constants.
     */
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PARTIAL = 'partial';
    public const STATUS_ABANDONED = 'abandoned';

    /**
     * Get the form that owns the response.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    /**
     * Get the user that submitted the response.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the answers for the response.
     */
    public function answers(): HasMany
    {
        return $this->hasMany(ResponseAnswer::class, 'response_id');
    }

    /**
     * Get answer for a specific question.
     */
    public function getAnswerForQuestion(int $questionId): ?ResponseAnswer
    {
        return $this->answers()->where('question_id', $questionId)->first();
    }
}
