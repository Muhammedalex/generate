@php
    $questionId = 'question_' . $question->id;
@endphp

@switch($question->type)
    @case(\App\Models\FormQuestion::TYPE_SHORT_TEXT)
    @case(\App\Models\FormQuestion::TYPE_EMAIL)
    @case(\App\Models\FormQuestion::TYPE_URL)
    @case(\App\Models\FormQuestion::TYPE_PHONE)
        <input type="{{ $question->type === \App\Models\FormQuestion::TYPE_EMAIL ? 'email' : ($question->type === \App\Models\FormQuestion::TYPE_URL ? 'url' : ($question->type === \App\Models\FormQuestion::TYPE_PHONE ? 'tel' : 'text')) }}" 
               id="{{ $questionId }}"
               name="answers[{{ $question->id }}]"
               value="{{ old('answers.' . $question->id) }}"
               @if($question->is_required) required @endif
               placeholder="{{ $question->settings['placeholder'] ?? '' }}"
               maxlength="{{ $question->settings['max_length'] ?? '' }}"
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
        @break

    @case(\App\Models\FormQuestion::TYPE_LONG_TEXT)
        <textarea id="{{ $questionId }}"
                  name="answers[{{ $question->id }}]"
                  rows="4"
                  @if($question->is_required) required @endif
                  placeholder="{{ $question->settings['placeholder'] ?? '' }}"
                  maxlength="{{ $question->settings['max_length'] ?? '' }}"
                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">{{ old('answers.' . $question->id) }}</textarea>
        @break

    @case(\App\Models\FormQuestion::TYPE_MULTIPLE_CHOICE)
        <div class="space-y-2">
            @foreach($question->options as $option)
                <label class="flex items-center">
                    <input type="radio" 
                           name="answers[{{ $question->id }}]"
                           value="{{ $option->id }}"
                           @if($question->is_required) required @endif
                           class="mr-2 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-gray-700 dark:text-gray-300">{{ $option->getTranslatedOptionText() }}</span>
                </label>
            @endforeach
        </div>
        @break

    @case(\App\Models\FormQuestion::TYPE_CHECKBOX)
        <div class="space-y-2">
            @foreach($question->options as $option)
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="answers[{{ $question->id }}][]"
                           value="{{ $option->id }}"
                           class="mr-2 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-gray-700 dark:text-gray-300">{{ $option->getTranslatedOptionText() }}</span>
                </label>
            @endforeach
        </div>
        @break

    @case(\App\Models\FormQuestion::TYPE_DROPDOWN)
        <select id="{{ $questionId }}"
                name="answers[{{ $question->id }}]"
                @if($question->is_required) required @endif
                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
            <option value="">{{ __('forms.select_option') }}</option>
            @foreach($question->options as $option)
                <option value="{{ $option->id }}" {{ old('answers.' . $question->id) == $option->id ? 'selected' : '' }}>
                    {{ $option->getTranslatedOptionText() }}
                </option>
            @endforeach
        </select>
        @break

    @case(\App\Models\FormQuestion::TYPE_NUMBER)
        <input type="number" 
               id="{{ $questionId }}"
               name="answers[{{ $question->id }}]"
               value="{{ old('answers.' . $question->id) }}"
               @if($question->is_required) required @endif
               min="{{ $question->settings['min_value'] ?? '' }}"
               max="{{ $question->settings['max_value'] ?? '' }}"
               step="{{ $question->settings['decimal_places'] ?? 1 }}"
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
        @break

    @case(\App\Models\FormQuestion::TYPE_DATE)
        <input type="date" 
               id="{{ $questionId }}"
               name="answers[{{ $question->id }}]"
               value="{{ old('answers.' . $question->id) }}"
               @if($question->is_required) required @endif
               min="{{ $question->settings['min_date'] ?? '' }}"
               max="{{ $question->settings['max_date'] ?? '' }}"
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
        @break

    @case(\App\Models\FormQuestion::TYPE_TIME)
        <input type="time" 
               id="{{ $questionId }}"
               name="answers[{{ $question->id }}]"
               value="{{ old('answers.' . $question->id) }}"
               @if($question->is_required) required @endif
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
        @break

    @case(\App\Models\FormQuestion::TYPE_DATETIME)
        <input type="datetime-local" 
               id="{{ $questionId }}"
               name="answers[{{ $question->id }}]"
               value="{{ old('answers.' . $question->id) }}"
               @if($question->is_required) required @endif
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
        @break

    @case(\App\Models\FormQuestion::TYPE_YES_NO)
        <div class="space-y-2">
            <label class="flex items-center">
                <input type="radio" 
                       name="answers[{{ $question->id }}]"
                       value="1"
                       @if($question->is_required) required @endif
                       class="mr-2 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700 dark:text-gray-300">{{ __('forms.yes') }}</span>
            </label>
            <label class="flex items-center">
                <input type="radio" 
                       name="answers[{{ $question->id }}]"
                       value="0"
                       @if($question->is_required) required @endif
                       class="mr-2 text-indigo-600 focus:ring-indigo-500">
                <span class="text-gray-700 dark:text-gray-300">{{ __('forms.no') }}</span>
            </label>
        </div>
        @break

    @case(\App\Models\FormQuestion::TYPE_FILE_UPLOAD)
        <input type="file" 
               id="{{ $questionId }}"
               name="answers[{{ $question->id }}]"
               @if($question->is_required) required @endif
               accept="{{ implode(',', $question->settings['allowed_file_types'] ?? []) }}"
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
        @break

    @case(\App\Models\FormQuestion::TYPE_LINEAR_SCALE)
        <div class="flex items-center gap-4">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $question->settings['min_label'] ?? '1' }}</span>
            <div class="flex-1 flex items-center gap-2">
                @for($i = ($question->settings['min_value'] ?? 1); $i <= ($question->settings['max_value'] ?? 5); $i++)
                    <label class="flex items-center">
                        <input type="radio" 
                               name="answers[{{ $question->id }}]"
                               value="{{ $i }}"
                               @if($question->is_required) required @endif
                               class="mr-1 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-gray-700 dark:text-gray-300">{{ $i }}</span>
                    </label>
                @endfor
            </div>
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $question->settings['max_label'] ?? '5' }}</span>
        </div>
        @break

    @default
        <input type="text" 
               id="{{ $questionId }}"
               name="answers[{{ $question->id }}]"
               value="{{ old('answers.' . $question->id) }}"
               @if($question->is_required) required @endif
               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
@endswitch

