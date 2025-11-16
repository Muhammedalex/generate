<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Company;
use Illuminate\Support\Facades\Storage;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        
        if (!$admin) {
            $this->command->warn('Admin user not found. Please run UserSeeder first.');
            return;
        }

        // Create 10 companies with full data
        for ($i = 1; $i <= 10; $i++) {
            $company = Company::factory()->create([
                'user_id' => $admin->id,
                'email' => "company{$i}@example.com",
                'phone' => '+20' . fake()->numerify('##########'),
                'website' => fake()->url(),
                'address' => fake()->address(),
                'primary_color' => fake()->hexColor(),
                'secondary_color' => fake()->hexColor(),
                'accent_color' => fake()->hexColor(),
                'is_active' => fake()->boolean(80), // 80% active
            ]);

            // Set translations
            $company->setTranslations('name', [
                'en' => fake()->company(),
                'ar' => fake('ar_SA')->company(),
            ]);

            $company->setTranslations('description', [
                'en' => fake()->paragraph(3),
                'ar' => fake('ar_SA')->paragraph(3),
            ]);

            // Set social links
            $socialLinks = [];
            $socialPlatforms = [
                'facebook', 'twitter', 'instagram', 'linkedin', 'youtube',
                'tiktok', 'whatsapp', 'telegram', 'snapchat', 'pinterest',
            ];

            foreach ($socialPlatforms as $platform) {
                if (fake()->boolean(60)) { // 60% chance to have each platform
                    $socialLinks[$platform] = fake()->url();
                }
            }

            $company->update(['social_links' => $socialLinks]);

            // Generate slug (will be auto-generated, but ensure it's set)
            $company->refresh();
        }

        $this->command->info('Companies seeded successfully!');
    }
}

