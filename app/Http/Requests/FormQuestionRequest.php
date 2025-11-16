<?php

namespace App\Http\Requests;

use App\Models\FormQuestion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FormQuestionRequest extends FormRequest
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
        $questionTypes = array_keys(FormQuestion::getTypes());

        return [
            'form_id' => ['required', 'exists:forms,id'],
            'section_id' => ['nullable', 'exists:form_sections,id'],
            'type' => ['required', Rule::in($questionTypes)],
            'question_text_translations' => ['required', 'array'],
            'question_text_translations.en' => ['required', 'string'],
            'question_text_translations.ar' => ['nullable', 'string'],
            'help_text_translations' => ['nullable', 'array'],
            'help_text_translations.en' => ['nullable', 'string'],
            'help_text_translations.ar' => ['nullable', 'string'],
            'order' => ['nullable', 'integer', 'min:0'],
            'is_required' => ['nullable', 'boolean'],
            'settings' => ['nullable', 'array'],
            'conditional_logic' => ['nullable', 'array'],
            'options' => ['nullable', 'array'],
            'options.*.option_text_translations' => ['required_with:options', 'array'],
            'options.*.option_text_translations.en' => ['required_with:options', 'string', 'max:255'],
            'options.*.option_text_translations.ar' => ['nullable', 'string', 'max:255'],
            'options.*.order' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
