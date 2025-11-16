@extends('layouts.app')

@section('title', __('forms.view_response') . ' - ' . $form->getTranslatedTitle())

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ __('forms.view_response') }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ $form->getTranslatedTitle() }}</p>
                </div>
                <a href="{{ route('forms.responses', $form) }}" 
                   class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ __('forms.back_to_responses') }}
                </a>
            </div>
        </div>

        <!-- Response Details -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Response Information</h2>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('forms.submitted_at') }}</p>
                    <p class="text-gray-900 dark:text-white">{{ $response->submitted_at?->format('Y-m-d H:i:s') ?? '-' }}</p>
                </div>
                @if($response->email)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('forms.email') }}</p>
                    <p class="text-gray-900 dark:text-white">{{ $response->email }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('forms.status') }}</p>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($response->status === 'completed') text-green-800 bg-green-100 dark:bg-green-900 dark:text-green-200
                        @elseif($response->status === 'partial') text-yellow-800 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-200
                        @else text-red-800 bg-red-100 dark:bg-red-900 dark:text-red-200
                        @endif">
                        {{ __('forms.' . $response->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Answers -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-6">Answers</h2>
            <div class="space-y-6">
                @foreach($form->questions()->orderBy('order')->get() as $question)
                    @php
                        $answer = $response->getAnswerForQuestion($question->id);
                    @endphp
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $question->getTranslatedQuestionText() }}
                        </h3>
                        <div class="text-gray-600 dark:text-gray-400">
                            @if($answer)
                                @if($answer->answer_text)
                                    {{ $answer->answer_text }}
                                @elseif($answer->answer_number !== null)
                                    {{ $answer->answer_number }}
                                @elseif($answer->answer_boolean !== null)
                                    {{ $answer->answer_boolean ? __('forms.yes') : __('forms.no') }}
                                @elseif($answer->answer_date)
                                    {{ $answer->answer_date->format('Y-m-d') }}
                                @elseif($answer->answer_json)
                                    @if(is_array($answer->answer_json))
                                        @php
                                            $optionIds = $answer->answer_json;
                                            $optionTexts = [];
                                            foreach ($optionIds as $optionId) {
                                                $option = $question->options()->find($optionId);
                                                if ($option) {
                                                    $optionTexts[] = $option->getTranslatedOptionText();
                                                } else {
                                                    $optionTexts[] = $optionId;
                                                }
                                            }
                                        @endphp
                                        {{ implode(', ', $optionTexts) }}
                                    @else
                                        {{ json_encode($answer->answer_json) }}
                                    @endif
                                @elseif($answer->answer_text && in_array($question->type, [\App\Models\FormQuestion::TYPE_MULTIPLE_CHOICE, \App\Models\FormQuestion::TYPE_DROPDOWN]))
                                    @php
                                        // For single choice questions, answer_text contains the option ID
                                        $option = $question->options()->find($answer->answer_text);
                                        $displayText = $option ? $option->getTranslatedOptionText() : $answer->answer_text;
                                    @endphp
                                    {{ $displayText }}
                                @elseif($answer->file_path)
                                    <a href="{{ \Illuminate\Support\Facades\Storage::url($answer->file_path) }}" 
                                       target="_blank"
                                       class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
                                        {{ __('forms.download_file') }}
                                    </a>
                                @else
                                    -
                                @endif
                            @else
                                <span class="text-gray-400">-</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

