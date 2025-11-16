<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormBuilderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $formId = $this->route('form')?->id;

        return [
            'title_translations' => ['required', 'array'],
            'title_translations.en' => ['required', 'string', 'max:255'],
            'title_translations.ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('forms', 'slug')->ignore($formId)],
            'description_translations' => ['nullable', 'array'],
            'description_translations.en' => ['nullable', 'string'],
            'description_translations.ar' => ['nullable', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'published', 'closed'])],
            'settings' => ['nullable', 'array'],
            'appearance' => ['nullable', 'array'],
            'allow_multiple' => ['nullable', 'boolean'],
            'require_auth' => ['nullable', 'boolean'],
            'collect_email' => ['nullable', 'boolean'],
            'show_progress' => ['nullable', 'boolean'],
            'randomize_questions' => ['nullable', 'boolean'],
            'expires_at' => ['nullable', 'date', 'after:starts_at'],
            'starts_at' => ['nullable', 'date'],
            'thank_you_message_translations' => ['nullable', 'array'],
            'thank_you_message_translations.en' => ['nullable', 'string'],
            'thank_you_message_translations.ar' => ['nullable', 'string'],
            'redirect_url' => ['nullable', 'url', 'max:255'],
        ];
    }
}
