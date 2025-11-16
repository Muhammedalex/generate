@extends('layouts.app')

@section('title', __('forms.responses') . ' - ' . $form->getTranslatedTitle())

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $form->getTranslatedTitle() }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('forms.responses') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('forms.responses.export', $form) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                        {{ __('forms.export') }} CSV
                    </a>
                    <a href="{{ route('forms.builder', $form) }}" 
                       class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        {{ __('forms.builder') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{{ __('forms.total_responses') }}</h3>
                <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $statistics['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{{ __('forms.completed') }}</h3>
                <p class="text-3xl font-bold text-green-600">{{ $statistics['completed'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{{ __('forms.partial') }}</h3>
                <p class="text-3xl font-bold text-yellow-600">{{ $statistics['partial'] }}</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">{{ __('forms.completion_rate') }}</h3>
                <p class="text-3xl font-bold text-indigo-600">{{ $statistics['completion_rate'] }}%</p>
            </div>
        </div>

        <!-- Responses Table -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                ID
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('forms.submitted_at') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('forms.email') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                {{ __('forms.status') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse($responses as $response)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                #{{ $response->id }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $response->submitted_at?->format('Y-m-d H:i') ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                {{ $response->email ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    @if($response->status === 'completed') text-green-800 bg-green-100 dark:bg-green-900 dark:text-green-200
                                    @elseif($response->status === 'partial') text-yellow-800 bg-yellow-100 dark:bg-yellow-900 dark:text-yellow-200
                                    @else text-red-800 bg-red-100 dark:bg-red-900 dark:text-red-200
                                    @endif">
                                    {{ __('forms.' . $response->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <a href="{{ route('forms.responses.show', [$form, $response]) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    {{ __('forms.view_response') }}
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                {{ __('forms.no_responses') }}
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($responses->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $responses->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

