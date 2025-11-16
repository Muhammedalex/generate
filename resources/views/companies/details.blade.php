@extends('layouts.app')

@section('title', $company->name . ' - ' . __('companies.company_details'))

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ $company->name }}</h1>
                    <p class="text-gray-600 dark:text-gray-400">{{ __('companies.company_details') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('company.show', $company) }}" 
                       target="_blank"
                       class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition">
                        {{ __('companies.view_public_profile') }}
                    </a>
                    <a href="{{ route('companies.edit', $company) }}" 
                       class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition">
                        {{ __('companies.edit') }}
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded-lg">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Company Information -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('companies.company_details') }}</h2>
                    
                    <div class="space-y-6">
                        <!-- Logo -->
                        @if($company->logo_path)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.logo') }}</label>
                            <img src="{{ Storage::url($company->logo_path) }}" 
                                 alt="{{ $company->name }}" 
                                 class="h-32 w-32 object-contain rounded-lg border border-gray-300 dark:border-gray-600 p-2">
                        </div>
                        @endif

                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.name') }}</label>
                            <p class="text-lg text-gray-900 dark:text-white">{{ $company->name }}</p>
                        </div>

                        <!-- Description -->
                        @if($company->description)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.description') }}</label>
                            <p class="text-gray-900 dark:text-white">{{ $company->description }}</p>
                        </div>
                        @endif

                        <!-- Contact Information -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @if($company->email)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.email') }}</label>
                                <a href="mailto:{{ $company->email }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $company->email }}
                                </a>
                            </div>
                            @endif

                            @if($company->phone)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.phone') }}</label>
                                <a href="tel:{{ $company->phone }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $company->phone }}
                                </a>
                            </div>
                            @endif

                            @if($company->website)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.website') }}</label>
                                <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $company->website }}
                                </a>
                            </div>
                            @endif

                            @if($company->address)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.address') }}</label>
                                <p class="text-gray-900 dark:text-white">{{ $company->address }}</p>
                            </div>
                            @endif
                        </div>

                        <!-- Brand Colors -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">{{ __('companies.brand_identity') }}</label>
                            <div class="flex gap-4">
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('companies.primary_color') }}</label>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-12 rounded-lg border-2 border-gray-300 dark:border-gray-600" style="background-color: {{ $company->primary_color }}"></div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $company->primary_color }}</span>
                                    </div>
                                </div>
                                @if($company->secondary_color)
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('companies.secondary_color') }}</label>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-12 rounded-lg border-2 border-gray-300 dark:border-gray-600" style="background-color: {{ $company->secondary_color }}"></div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $company->secondary_color }}</span>
                                    </div>
                                </div>
                                @endif
                                @if($company->accent_color)
                                <div>
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">{{ __('companies.accent_color') }}</label>
                                    <div class="flex items-center gap-2">
                                        <div class="w-12 h-12 rounded-lg border-2 border-gray-300 dark:border-gray-600" style="background-color: {{ $company->accent_color }}"></div>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $company->accent_color }}</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.is_active') }}</label>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $company->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $company->is_active ? __('dashboard.active') : __('companies.inactive') }}
                            </span>
                        </div>

                        <!-- Social Links -->
                        @if($company->social_links && count(array_filter($company->social_links)))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">{{ __('companies.social_links') }}</label>
                            <div class="flex flex-wrap gap-3">
                                @foreach($company->social_links as $platform => $url)
                                    @if(!empty($url))
                                    <a href="{{ $url }}" 
                                       target="_blank" 
                                       rel="noopener noreferrer"
                                       class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm">
                                        {{ ucfirst(str_replace('_', ' ', $platform)) }}
                                    </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- QR Code Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('companies.qr_code') }}</h3>
                    
                    <!-- QR Code Display -->
                    <div class="text-center mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <img src="{{ route('companies.qrcode.show', ['company' => $company, 'size' => 250]) }}" 
                             alt="QR Code" 
                             class="mx-auto border-4 border-white dark:border-gray-600 rounded-lg p-2 bg-white">
                    </div>

                    <!-- Actions -->
                    <div class="space-y-3">
                        <a href="{{ route('companies.qrcode.show', $company) }}" 
                           target="_blank"
                           class="block w-full text-center px-4 py-3 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition shadow-lg">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            {{ __('companies.view_qr') }}
                        </a>
                        <a href="{{ route('companies.qrcode.download', $company) }}" 
                           class="block w-full text-center px-4 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition shadow-lg">
                            <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            {{ __('companies.download_qr') }}
                        </a>
                    </div>

                    <!-- Public URL -->
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('companies.public_profile') }}</label>
                        <div class="flex items-center gap-2">
                            <input type="text" 
                                   value="{{ route('company.show', $company) }}" 
                                   readonly
                                   class="flex-1 px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                            <button onclick="copyToClipboard('{{ route('company.show', $company) }}')" 
                                    class="px-3 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition"
                                    title="{{ __('companies.copy_link') }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4">{{ __('companies.statistics') }}</h3>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('companies.created_at') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $company->created_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('companies.updated_at') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $company->updated_at->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('companies.slug') }}</p>
                            <p class="text-lg font-semibold text-gray-900 dark:text-white font-mono">{{ $company->slug }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('{{ __('companies.link_copied') }}');
    });
}
</script>
@endsection

