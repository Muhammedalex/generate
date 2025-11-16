@extends('layouts.app')

@section('title', __('forms.forms'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ __('forms.forms') }}</h1>
                <p class="text-gray-600 dark:text-gray-400">{{ __('forms.total_responses') }}: {{ $forms->total() }}</p>
            </div>
            <a href="{{ route('forms.create') }}" 
               class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                {{ __('forms.create_form') }}
            </a>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        @if($forms->count() > 0)
            <!-- Forms Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($forms as $form)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <!-- Form Header -->
                    <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600 p-6 flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-white mb-1">{{ $form->getTranslatedTitle() }}</h3>
                            <p class="text-sm text-indigo-100">{{ Str::limit($form->getTranslatedDescription() ?: '', 60) }}</p>
                        </div>
                    </div>

                    <!-- Form Content -->
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <span class="px-3 py-1 text-xs font-semibold rounded-full
                                @if($form->status === 'published') text-green-800 bg-green-100 dark:bg-green-900 dark:text-green-200
                                @elseif($form->status === 'closed') text-red-800 bg-red-100 dark:bg-red-900 dark:text-red-200
                                @else text-gray-800 bg-gray-100 dark:bg-gray-700 dark:text-gray-200
                                @endif">
                                {{ __('forms.' . $form->status) }}
                            </span>
                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $form->total_responses }} {{ __('forms.responses') }}
                            </span>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2 mt-6">
                            <a href="{{ route('forms.show', $form) }}" 
                               target="_blank"
                               class="flex-1 text-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                                {{ __('forms.view') }}
                            </a>
                            <a href="{{ route('forms.builder', $form) }}" 
                               class="px-4 py-2 bg-purple-600 text-white rounded-lg font-medium hover:bg-purple-700 transition"
                               title="{{ __('forms.builder') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('forms.responses', $form) }}" 
                               class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition"
                               title="{{ __('forms.responses') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </a>
                            <form action="{{ route('forms.destroy', $form) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('forms.confirm_delete') }}');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="px-4 py-2 bg-red-600 text-white rounded-lg font-medium hover:bg-red-700 transition"
                                        title="{{ __('forms.delete') }}">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $forms->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-xl shadow-lg">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ __('forms.no_forms') }}</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">{{ __('forms.create_first_form') }}</p>
                <a href="{{ route('forms.create') }}" 
                   class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 transition shadow-lg">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ __('forms.create_form') }}
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

