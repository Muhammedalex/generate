<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Translation extends Model
{
    use HasFactory;

    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'locale',
        'field_name',
        'value',
    ];

    /**
     * Get the parent translatable model.
     */
    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope to get translation for specific locale and field.
     */
    public function scopeForLocale($query, string $locale, string $field)
    {
        return $query->where('locale', $locale)->where('field_name', $field);
    }
}
