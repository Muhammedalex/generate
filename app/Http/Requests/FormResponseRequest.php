<?php

namespace App\Http\Requests;

use App\Models\Form;
use App\Models\FormQuestion;
use Illuminate\Foundation\Http\FormRequest;

class FormResponseRequest extends FormRequest
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
        $form = $this->route('form');
        
        if (!$form instanceof Form) {
            return [];
        }

        $rules = [];

        // Email collection if enabled
        if ($form->collect_email) {
            $rules['email'] = ['required', 'email', 'max:255'];
        }

        // Validate answers based on form questions
        $rules['answers'] = ['required', 'array'];
        
        foreach ($form->questions as $question) {
            $key = "answers.{$question->id}";
            
            if ($question->is_required) {
                $rules[$key] = ['required'];
            } else {
                $rules[$key] = ['nullable'];
            }

            // Type-specific validation
            switch ($question->type) {
                case FormQuestion::TYPE_EMAIL:
                    $rules[$key][] = 'email';
                    break;
                case FormQuestion::TYPE_URL:
                    $rules[$key][] = 'url';
                    break;
                case FormQuestion::TYPE_NUMBER:
                    $rules[$key][] = 'numeric';
                    if (isset($question->settings['min_value'])) {
                        $rules[$key][] = 'min:' . $question->settings['min_value'];
                    }
                    if (isset($question->settings['max_value'])) {
                        $rules[$key][] = 'max:' . $question->settings['max_value'];
                    }
                    break;
                case FormQuestion::TYPE_FILE_UPLOAD:
                    $rules[$key] = array_merge($rules[$key], ['file']);
                    if (isset($question->settings['max_file_size'])) {
                        $rules[$key][] = 'max:' . ($question->settings['max_file_size'] / 1024); // Convert to KB
                    }
                    break;
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'answers.required' => 'Please answer all required questions.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please provide a valid email address.',
        ];
    }
}
