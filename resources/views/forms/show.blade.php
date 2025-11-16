@extends('layouts.app')

@section('title', $form->getTranslatedTitle())

@section('content')
<div class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <!-- Form Header -->
            <div class="mb-8">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-4">{{ $form->getTranslatedTitle() }}</h1>
                @if($form->description)
                    <p class="text-gray-600 dark:text-gray-400">{{ $form->getTranslatedDescription() }}</p>
                @endif
            </div>

            @if(session('success'))
            <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-6 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded-lg">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Form -->
            <form action="{{ route('forms.submit', $form) }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if($form->collect_email)
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('forms.email') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                </div>
                @endif

                <!-- Questions -->
                @php
                    // Get all questions from sections and without sections, properly ordered
                    $allQuestions = collect();
                    
                    // Add questions from sections
                    foreach ($form->sections as $section) {
                        foreach ($section->questions as $question) {
                            $allQuestions->push($question);
                        }
                    }
                    
                    // Add questions without sections
                    foreach ($form->questions as $question) {
                        if (!$question->section_id) {
                            $allQuestions->push($question);
                        }
                    }
                    
                    // Sort by order
                    $allQuestions = $allQuestions->sortBy('order');
                    $currentSection = null;
                @endphp

                @foreach($allQuestions as $question)
                    @if($question->section_id && $question->section_id !== $currentSection?->id)
                        @php $currentSection = $question->section; @endphp
                        <div class="mb-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            @if($currentSection->getTranslatedTitle())
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                    {{ $currentSection->getTranslatedTitle() }}
                                </h2>
                            @endif
                            @if($currentSection->getTranslatedDescription())
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    {{ $currentSection->getTranslatedDescription() }}
                                </p>
                            @endif
                        </div>
                    @endif

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ $question->getTranslatedQuestionText() }}
                            @if($question->is_required)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        
                        @if($question->getTranslatedHelpText())
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                {{ $question->getTranslatedHelpText() }}
                            </p>
                        @endif

                        @include('forms.partials.question-input', ['question' => $question])
                    </div>
                @endforeach

                <!-- Submit Button -->
                <div class="mt-8">
                    <button type="submit" 
                            class="w-full px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition">
                        {{ __('forms.submit') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

