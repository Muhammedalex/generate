@extends('layouts.app')

@section('title', __('forms.edit_form'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('forms.edit_form') }}</h1>

            <form action="{{ route('forms.update', $form) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Title Translations -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('forms.title') }} <span class="text-red-500">*</span>
                    </label>
                    
                    <!-- English Title -->
                    <div class="mb-3">
                        <label for="title_translations_en" class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                            {{ __('common.english') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="title_translations_en" 
                               name="title_translations[en]" 
                               value="{{ old('title_translations.en', $form->getTranslatedTitle('en')) }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('title_translations.en')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Arabic Title -->
                    <div>
                        <label for="title_translations_ar" class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                            {{ __('common.arabic') }}
                        </label>
                        <input type="text" 
                               id="title_translations_ar" 
                               name="title_translations[ar]" 
                               value="{{ old('title_translations.ar', $form->getTranslatedTitle('ar')) }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('title_translations.ar')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Description Translations -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('forms.description') }}
                    </label>
                    
                    <!-- English Description -->
                    <div class="mb-3">
                        <label for="description_translations_en" class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                            {{ __('common.english') }}
                        </label>
                        <textarea id="description_translations_en" 
                                  name="description_translations[en]" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">{{ old('description_translations.en', $form->getTranslatedDescription('en')) }}</textarea>
                        @error('description_translations.en')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Arabic Description -->
                    <div>
                        <label for="description_translations_ar" class="block text-xs text-gray-600 dark:text-gray-400 mb-1">
                            {{ __('common.arabic') }}
                        </label>
                        <textarea id="description_translations_ar" 
                                  name="description_translations[ar]" 
                                  rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">{{ old('description_translations.ar', $form->getTranslatedDescription('ar')) }}</textarea>
                        @error('description_translations.ar')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="mb-6">
                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ __('forms.status') }}
                    </label>
                    <select id="status" 
                            name="status"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        <option value="draft" {{ old('status', $form->status) === 'draft' ? 'selected' : '' }}>{{ __('forms.draft') }}</option>
                        <option value="published" {{ old('status', $form->status) === 'published' ? 'selected' : '' }}>{{ __('forms.published') }}</option>
                        <option value="closed" {{ old('status', $form->status) === 'closed' ? 'selected' : '' }}>{{ __('forms.closed') }}</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('forms.index') }}" 
                       class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        {{ __('forms.cancel') }}
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        {{ __('forms.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
