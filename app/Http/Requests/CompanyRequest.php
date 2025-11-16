<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyRequest extends FormRequest
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
        $companyId = $this->route('company')?->id;

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_translations' => ['nullable', 'array'],
            'name_translations.en' => ['nullable', 'string', 'max:255'],
            'name_translations.ar' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('companies', 'slug')->ignore($companyId)],
            'description' => ['nullable', 'string'],
            'description_translations' => ['nullable', 'array'],
            'description_translations.en' => ['nullable', 'string'],
            'description_translations.ar' => ['nullable', 'string'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'website' => ['nullable', 'url', 'max:255'],
            'address' => ['nullable', 'string'],
            'primary_color' => ['required', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'secondary_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'accent_color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:5120'], // 5MB
            'social_links' => ['nullable', 'array'],
            'social_links.facebook' => ['nullable', 'url'],
            'social_links.twitter' => ['nullable', 'url'],
            'social_links.instagram' => ['nullable', 'url'],
            'social_links.linkedin' => ['nullable', 'url'],
            'social_links.youtube' => ['nullable', 'url'],
            'social_links.tiktok' => ['nullable', 'url'],
            'social_links.whatsapp' => ['nullable', 'url'],
            'social_links.telegram' => ['nullable', 'url'],
            'social_links.snapchat' => ['nullable', 'url'],
            'social_links.pinterest' => ['nullable', 'url'],
            'social_links.reddit' => ['nullable', 'url'],
            'social_links.discord' => ['nullable', 'url'],
            'social_links.github' => ['nullable', 'url'],
            'social_links.behance' => ['nullable', 'url'],
            'social_links.dribbble' => ['nullable', 'url'],
            'social_links.medium' => ['nullable', 'url'],
            'social_links.vimeo' => ['nullable', 'url'],
            'social_links.twitch' => ['nullable', 'url'],
            'social_links.soundcloud' => ['nullable', 'url'],
            'social_links.spotify' => ['nullable', 'url'],
            'social_links.apple_music' => ['nullable', 'url'],
            'social_links.xing' => ['nullable', 'url'],
            'social_links.vk' => ['nullable', 'url'],
            'social_links.wechat' => ['nullable', 'url'],
            'social_links.weibo' => ['nullable', 'url'],
            'social_links.line' => ['nullable', 'url'],
            'social_links.skype' => ['nullable', 'url'],
            'social_links.custom' => ['nullable', 'array'],
            'social_links.custom.*.name' => ['nullable', 'string', 'max:255'],
            'social_links.custom.*.url' => ['nullable', 'url'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
