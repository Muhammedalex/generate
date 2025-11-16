@extends('layouts.app')

@section('title', __('companies.create_company'))

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-900 dark:text-white mb-2">{{ __('companies.create_company') }}</h1>
            <p class="text-gray-600 dark:text-gray-400">{{ __('companies.company_details') }}</p>
        </div>

        <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data" class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            @csrf

            <!-- Basic Information -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('companies.company_details') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Company Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}"
                               required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.description') }}
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.email') }}
                        </label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.phone') }}
                        </label>
                        <input type="text" 
                               id="phone" 
                               name="phone" 
                               value="{{ old('phone') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Website -->
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.website') }}
                        </label>
                        <input type="url" 
                               id="website" 
                               name="website" 
                               value="{{ old('website') }}"
                               placeholder="https://example.com"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('website')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Address -->
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.address') }}
                        </label>
                        <input type="text" 
                               id="address" 
                               name="address" 
                               value="{{ old('address') }}"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('address')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Brand Identity -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('companies.brand_identity') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Primary Color -->
                    <div>
                        <label for="primary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.primary_color') }} <span class="text-red-500">*</span>
                        </label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="primary_color" 
                                   name="primary_color" 
                                   value="{{ old('primary_color', '#6366f1') }}"
                                   required
                                   class="h-10 w-20 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                            <input type="text" 
                                   value="{{ old('primary_color', '#6366f1') }}"
                                   pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        @error('primary_color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Secondary Color -->
                    <div>
                        <label for="secondary_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.secondary_color') }}
                        </label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="secondary_color" 
                                   name="secondary_color" 
                                   value="{{ old('secondary_color', '#8b5cf6') }}"
                                   class="h-10 w-20 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                            <input type="text" 
                                   value="{{ old('secondary_color', '#8b5cf6') }}"
                                   pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        @error('secondary_color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Accent Color -->
                    <div>
                        <label for="accent_color" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.accent_color') }}
                        </label>
                        <div class="flex gap-2">
                            <input type="color" 
                                   id="accent_color" 
                                   name="accent_color" 
                                   value="{{ old('accent_color', '#ec4899') }}"
                                   class="h-10 w-20 border border-gray-300 dark:border-gray-600 rounded-lg cursor-pointer">
                            <input type="text" 
                                   value="{{ old('accent_color', '#ec4899') }}"
                                   pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                                   class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        </div>
                        @error('accent_color')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Logo -->
                    <div class="md:col-span-3">
                        <label for="logo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.logo') }}
                        </label>
                        <input type="file" 
                               id="logo" 
                               name="logo" 
                               accept="image/*"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">PNG, JPG, SVG up to 5MB</p>
                        @error('logo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Social Media Links -->
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('companies.social_links') }}</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <!-- Facebook -->
                    <div>
                        <label for="social_links_facebook" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.facebook') }}
                        </label>
                        <input type="url" 
                               id="social_links_facebook" 
                               name="social_links[facebook]" 
                               value="{{ old('social_links.facebook') }}"
                               placeholder="https://facebook.com/yourpage"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Twitter/X -->
                    <div>
                        <label for="social_links_twitter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.twitter') }}
                        </label>
                        <input type="url" 
                               id="social_links_twitter" 
                               name="social_links[twitter]" 
                               value="{{ old('social_links.twitter') }}"
                               placeholder="https://twitter.com/yourhandle"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Instagram -->
                    <div>
                        <label for="social_links_instagram" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.instagram') }}
                        </label>
                        <input type="url" 
                               id="social_links_instagram" 
                               name="social_links[instagram]" 
                               value="{{ old('social_links.instagram') }}"
                               placeholder="https://instagram.com/yourhandle"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- LinkedIn -->
                    <div>
                        <label for="social_links_linkedin" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.linkedin') }}
                        </label>
                        <input type="url" 
                               id="social_links_linkedin" 
                               name="social_links[linkedin]" 
                               value="{{ old('social_links.linkedin') }}"
                               placeholder="https://linkedin.com/company/yourcompany"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- YouTube -->
                    <div>
                        <label for="social_links_youtube" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.youtube') }}
                        </label>
                        <input type="url" 
                               id="social_links_youtube" 
                               name="social_links[youtube]" 
                               value="{{ old('social_links.youtube') }}"
                               placeholder="https://youtube.com/@yourchannel"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- TikTok -->
                    <div>
                        <label for="social_links_tiktok" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.tiktok') }}
                        </label>
                        <input type="url" 
                               id="social_links_tiktok" 
                               name="social_links[tiktok]" 
                               value="{{ old('social_links.tiktok') }}"
                               placeholder="https://tiktok.com/@yourhandle"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- WhatsApp -->
                    <div>
                        <label for="social_links_whatsapp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.whatsapp') }}
                        </label>
                        <input type="url" 
                               id="social_links_whatsapp" 
                               name="social_links[whatsapp]" 
                               value="{{ old('social_links.whatsapp') }}"
                               placeholder="https://wa.me/1234567890"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Telegram -->
                    <div>
                        <label for="social_links_telegram" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.telegram') }}
                        </label>
                        <input type="url" 
                               id="social_links_telegram" 
                               name="social_links[telegram]" 
                               value="{{ old('social_links.telegram') }}"
                               placeholder="https://t.me/yourchannel"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Snapchat -->
                    <div>
                        <label for="social_links_snapchat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.snapchat') }}
                        </label>
                        <input type="url" 
                               id="social_links_snapchat" 
                               name="social_links[snapchat]" 
                               value="{{ old('social_links.snapchat') }}"
                               placeholder="https://snapchat.com/add/yourusername"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Pinterest -->
                    <div>
                        <label for="social_links_pinterest" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.pinterest') }}
                        </label>
                        <input type="url" 
                               id="social_links_pinterest" 
                               name="social_links[pinterest]" 
                               value="{{ old('social_links.pinterest') }}"
                               placeholder="https://pinterest.com/yourprofile"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Reddit -->
                    <div>
                        <label for="social_links_reddit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.reddit') }}
                        </label>
                        <input type="url" 
                               id="social_links_reddit" 
                               name="social_links[reddit]" 
                               value="{{ old('social_links.reddit') }}"
                               placeholder="https://reddit.com/user/yourusername"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Discord -->
                    <div>
                        <label for="social_links_discord" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.discord') }}
                        </label>
                        <input type="url" 
                               id="social_links_discord" 
                               name="social_links[discord]" 
                               value="{{ old('social_links.discord') }}"
                               placeholder="https://discord.gg/yourserver"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- GitHub -->
                    <div>
                        <label for="social_links_github" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.github') }}
                        </label>
                        <input type="url" 
                               id="social_links_github" 
                               name="social_links[github]" 
                               value="{{ old('social_links.github') }}"
                               placeholder="https://github.com/yourusername"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Behance -->
                    <div>
                        <label for="social_links_behance" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.behance') }}
                        </label>
                        <input type="url" 
                               id="social_links_behance" 
                               name="social_links[behance]" 
                               value="{{ old('social_links.behance') }}"
                               placeholder="https://behance.net/yourprofile"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Dribbble -->
                    <div>
                        <label for="social_links_dribbble" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.dribbble') }}
                        </label>
                        <input type="url" 
                               id="social_links_dribbble" 
                               name="social_links[dribbble]" 
                               value="{{ old('social_links.dribbble') }}"
                               placeholder="https://dribbble.com/yourprofile"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Medium -->
                    <div>
                        <label for="social_links_medium" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.medium') }}
                        </label>
                        <input type="url" 
                               id="social_links_medium" 
                               name="social_links[medium]" 
                               value="{{ old('social_links.medium') }}"
                               placeholder="https://medium.com/@yourusername"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Vimeo -->
                    <div>
                        <label for="social_links_vimeo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.vimeo') }}
                        </label>
                        <input type="url" 
                               id="social_links_vimeo" 
                               name="social_links[vimeo]" 
                               value="{{ old('social_links.vimeo') }}"
                               placeholder="https://vimeo.com/yourchannel"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Twitch -->
                    <div>
                        <label for="social_links_twitch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.twitch') }}
                        </label>
                        <input type="url" 
                               id="social_links_twitch" 
                               name="social_links[twitch]" 
                               value="{{ old('social_links.twitch') }}"
                               placeholder="https://twitch.tv/yourchannel"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- SoundCloud -->
                    <div>
                        <label for="social_links_soundcloud" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.soundcloud') }}
                        </label>
                        <input type="url" 
                               id="social_links_soundcloud" 
                               name="social_links[soundcloud]" 
                               value="{{ old('social_links.soundcloud') }}"
                               placeholder="https://soundcloud.com/yourprofile"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Spotify -->
                    <div>
                        <label for="social_links_spotify" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.spotify') }}
                        </label>
                        <input type="url" 
                               id="social_links_spotify" 
                               name="social_links[spotify]" 
                               value="{{ old('social_links.spotify') }}"
                               placeholder="https://open.spotify.com/artist/yourid"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Apple Music -->
                    <div>
                        <label for="social_links_apple_music" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.apple_music') }}
                        </label>
                        <input type="url" 
                               id="social_links_apple_music" 
                               name="social_links[apple_music]" 
                               value="{{ old('social_links.apple_music') }}"
                               placeholder="https://music.apple.com/artist/yourid"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Xing -->
                    <div>
                        <label for="social_links_xing" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.xing') }}
                        </label>
                        <input type="url" 
                               id="social_links_xing" 
                               name="social_links[xing]" 
                               value="{{ old('social_links.xing') }}"
                               placeholder="https://xing.com/profile/yourprofile"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- VK -->
                    <div>
                        <label for="social_links_vk" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.vk') }}
                        </label>
                        <input type="url" 
                               id="social_links_vk" 
                               name="social_links[vk]" 
                               value="{{ old('social_links.vk') }}"
                               placeholder="https://vk.com/yourpage"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- WeChat -->
                    <div>
                        <label for="social_links_wechat" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.wechat') }}
                        </label>
                        <input type="url" 
                               id="social_links_wechat" 
                               name="social_links[wechat]" 
                               value="{{ old('social_links.wechat') }}"
                               placeholder="WeChat ID or QR code URL"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Weibo -->
                    <div>
                        <label for="social_links_weibo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.weibo') }}
                        </label>
                        <input type="url" 
                               id="social_links_weibo" 
                               name="social_links[weibo]" 
                               value="{{ old('social_links.weibo') }}"
                               placeholder="https://weibo.com/yourprofile"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Line -->
                    <div>
                        <label for="social_links_line" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.line') }}
                        </label>
                        <input type="url" 
                               id="social_links_line" 
                               name="social_links[line]" 
                               value="{{ old('social_links.line') }}"
                               placeholder="https://line.me/R/ti/p/@yourid"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Skype -->
                    <div>
                        <label for="social_links_skype" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            {{ __('companies.skype') }}
                        </label>
                        <input type="url" 
                               id="social_links_skype" 
                               name="social_links[skype]" 
                               value="{{ old('social_links.skype') }}"
                               placeholder="skype:yourusername?call"
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="mb-8">
                <label class="flex items-center">
                    <input type="checkbox" 
                           name="is_active" 
                           value="1"
                           {{ old('is_active', true) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('companies.is_active') }}</span>
                </label>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('companies.index') }}" 
                   class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    {{ __('companies.cancel') }}
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition shadow-lg">
                    {{ __('companies.save') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Sync color pickers with text inputs
document.addEventListener('DOMContentLoaded', function() {
    const colorInputs = ['primary_color', 'secondary_color', 'accent_color'];
    
    colorInputs.forEach(colorName => {
        const colorPicker = document.getElementById(colorName);
        const textInput = colorPicker.nextElementSibling;
        
        if (colorPicker && textInput) {
            colorPicker.addEventListener('input', function() {
                textInput.value = this.value;
            });
            
            textInput.addEventListener('input', function() {
                if (/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/.test(this.value)) {
                    colorPicker.value = this.value;
                }
            });
        }
    });
});
</script>
@endsection

