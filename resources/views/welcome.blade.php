@extends('layouts.app')

@section('title', __('common.welcome'))

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <!-- Hero Section -->
        <div class="text-center mb-16">
            <h1 class="text-5xl md:text-6xl font-extrabold text-gray-900 dark:text-white mb-4">
                {{ __('common.welcome_to') }} <span class="text-indigo-600 dark:text-indigo-400">{{ __('common.app_name') }}</span>
            </h1>
            <p class="text-xl text-gray-600 dark:text-gray-300 mb-8 max-w-2xl mx-auto">
                {{ __('common.welcome_description') }}
            </p>
            @guest
            <div class="flex justify-center gap-4">
                <a href="{{ route('register') }}" 
                   class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    {{ __('common.get_started') }}
                </a>
                <a href="{{ route('login') }}" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 text-base font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150 ease-in-out">
                    {{ __('common.sign_in') }}
                </a>
            </div>
            @endguest
        </div>

        <!-- Features Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-16">
            <!-- Company Data Feature -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center mb-4 gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-indigo-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('common.company_data') }}</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    {{ __('common.company_data_description') }}
                </p>
                <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400 list-none">
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ __('common.company_information_management') }}</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ __('common.brand_colors_logo') }}</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ __('common.qr_code_generation') }}</span>
                    </li>
                </ul>
                </div>

            <!-- Form Builder Feature -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 hover:shadow-xl transition-shadow duration-300">
                <div class="flex items-center mb-4 gap-4">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center h-12 w-12 rounded-md bg-blue-500 text-white">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                        </div>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('common.form_builder') }}</h2>
                </div>
                <p class="text-gray-600 dark:text-gray-300 mb-4">
                    {{ __('common.form_builder_description') }}
                </p>
                <ul class="space-y-2 text-sm text-gray-500 dark:text-gray-400 list-none" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">{{ __('common.question_types') }}</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">{{ __('common.full_customization') }}</span>
                    </li>
                    <li class="flex items-center gap-2">
                        <svg class="h-5 w-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">{{ __('common.response_analytics') }}</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- CTA Section -->
        @guest
        <div class="text-center bg-indigo-600 dark:bg-indigo-800 rounded-xl shadow-lg p-12">
            <h2 class="text-3xl font-bold text-white mb-4">{{ __('common.ready_to_get_started') }}</h2>
            <p class="text-indigo-100 mb-8 text-lg">{{ __('common.join_us_today') }}</p>
            <a href="{{ route('register') }}" 
               class="inline-flex items-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-indigo-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition duration-150 ease-in-out">
                {{ __('common.create_your_account') }}
            </a>
        </div>
        @endguest
    </div>
</div>
@endsection
