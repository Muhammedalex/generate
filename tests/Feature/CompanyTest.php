<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Translation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);

        $this->user = User::factory()->create([
            'email' => 'user@test.com',
            'role' => 'user',
        ]);
    }

    /** @test */
    public function admin_can_view_companies_index()
    {
        $response = $this->actingAs($this->admin)->get(route('companies.index'));

        $response->assertStatus(200);
        $response->assertViewIs('companies.index');
    }

    /** @test */
    public function admin_can_view_create_company_form()
    {
        $response = $this->actingAs($this->admin)->get(route('companies.create'));

        $response->assertStatus(200);
        $response->assertViewIs('companies.create');
    }

    /** @test */
    public function admin_can_create_company()
    {
        $companyData = [
            'name_translations' => [
                'en' => 'Test Company',
                'ar' => 'شركة تجريبية',
            ],
            'description_translations' => [
                'en' => 'Test description',
                'ar' => 'وصف تجريبي',
            ],
            'email' => 'test@company.com',
            'phone' => '1234567890',
            'website' => 'https://test.com',
            'address' => '123 Test St',
            'primary_color' => '#FF0000',
            'secondary_color' => '#00FF00',
            'accent_color' => '#0000FF',
            'social_links' => [
                'facebook' => 'https://facebook.com/test',
                'twitter' => 'https://twitter.com/test',
            ],
            'is_active' => true,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('companies.store'), $companyData);

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', [
            'email' => 'test@company.com',
            'user_id' => $this->admin->id,
        ]);

        $company = Company::where('email', 'test@company.com')->first();
        $this->assertNotNull($company);
        $this->assertEquals('Test Company', $company->getTranslatedName('en'));
        $this->assertEquals('شركة تجريبية', $company->getTranslatedName('ar'));
        $this->assertEquals('test-company', $company->slug);
    }

    /** @test */
    public function admin_can_create_company_with_logo()
    {
        Storage::fake('public');

        $logo = UploadedFile::fake()->image('logo.jpg', 100, 100);

        $companyData = [
            'name_translations' => [
                'en' => 'Test Company',
                'ar' => 'شركة تجريبية',
            ],
            'email' => 'test@company.com',
            'primary_color' => '#FF0000',
            'logo' => $logo,
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('companies.store'), $companyData);

        $response->assertRedirect();
        
        $company = Company::where('email', 'test@company.com')->first();
        $this->assertNotNull($company, 'Company was not created. Check validation errors.');
        $this->assertNotNull($company->logo_path);
        Storage::disk('public')->assertExists($company->logo_path);
    }

    /** @test */
    public function admin_can_view_company_details()
    {
        $company = Company::factory()->create(['user_id' => $this->admin->id]);
        $company->setTranslations('name', ['en' => 'Test Company', 'ar' => 'شركة تجريبية']);

        $response = $this->actingAs($this->admin)
            ->get(route('companies.details', $company));

        $response->assertStatus(200);
        $response->assertViewIs('companies.details');
    }

    /** @test */
    public function admin_can_view_edit_company_form()
    {
        $company = Company::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('companies.edit', $company));

        $response->assertStatus(200);
        $response->assertViewIs('companies.edit');
    }

    /** @test */
    public function admin_can_update_company()
    {
        $company = Company::factory()->create(['user_id' => $this->admin->id]);
        $company->setTranslations('name', ['en' => 'Original Name', 'ar' => 'الاسم الأصلي']);

        $updateData = [
            'name_translations' => [
                'en' => 'Updated Company',
                'ar' => 'شركة محدثة',
            ],
            'description_translations' => [
                'en' => 'Updated description',
                'ar' => 'وصف محدث',
            ],
            'email' => 'updated@company.com',
            'phone' => '9876543210',
            'primary_color' => $company->primary_color,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('companies.update', $company), $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'email' => 'updated@company.com',
        ]);

        $company->refresh();
        $this->assertEquals('Updated Company', $company->getTranslatedName('en'));
        $this->assertEquals('شركة محدثة', $company->getTranslatedName('ar'));
    }

    /** @test */
    public function admin_can_update_company_logo()
    {
        Storage::fake('public');

        $company = Company::factory()->create([
            'user_id' => $this->admin->id,
            'logo_path' => 'logos/old-logo.jpg',
        ]);
        $company->setTranslations('name', ['en' => 'Test Company']);

        Storage::disk('public')->put('logos/old-logo.jpg', 'fake content');

        $newLogo = UploadedFile::fake()->image('new-logo.jpg', 100, 100);

        $updateData = [
            'name_translations' => [
                'en' => 'Test Company',
            ],
            'email' => $company->email,
            'primary_color' => $company->primary_color,
            'logo' => $newLogo,
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('companies.update', $company), $updateData);

        $response->assertRedirect();
        
        $company->refresh();
        $this->assertNotNull($company->logo_path);
        $this->assertNotEquals('logos/old-logo.jpg', $company->logo_path);
        Storage::disk('public')->assertExists($company->logo_path);
    }

    /** @test */
    public function admin_can_delete_company()
    {
        $company = Company::factory()->create(['user_id' => $this->admin->id]);
        $company->setTranslations('name', ['en' => 'Test Company']);

        $response = $this->actingAs($this->admin)
            ->delete(route('companies.destroy', $company));

        $response->assertRedirect(route('companies.index'));
        $this->assertDatabaseMissing('companies', ['id' => $company->id]);
        
        // Check that translations are also deleted
        $this->assertDatabaseMissing('translations', [
            'translatable_type' => Company::class,
            'translatable_id' => $company->id,
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_companies()
    {
        $response = $this->actingAs($this->user)->get(route('companies.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_can_view_public_company_profile()
    {
        $company = Company::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => true,
        ]);
        $company->setTranslations('name', ['en' => 'Public Company', 'ar' => 'شركة عامة']);

        $response = $this->get(route('company.show', $company->slug));

        $response->assertStatus(200);
        $response->assertViewIs('companies.show');
    }

    /** @test */
    public function guest_cannot_view_inactive_company_profile()
    {
        $company = Company::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => false,
        ]);
        $company->setTranslations('name', ['en' => 'Inactive Company']);

        $response = $this->get(route('company.show', $company->slug));

        $response->assertStatus(404);
    }

    /** @test */
    public function owner_can_view_inactive_company_profile()
    {
        $company = Company::factory()->create([
            'user_id' => $this->admin->id,
            'is_active' => false,
        ]);
        $company->setTranslations('name', ['en' => 'Inactive Company']);

        $response = $this->actingAs($this->admin)
            ->get(route('company.show', $company->slug));

        $response->assertStatus(200);
    }

    /** @test */
    public function company_requires_english_name()
    {
        $companyData = [
            'name_translations' => [
                'ar' => 'شركة تجريبية',
            ],
            'email' => 'test@company.com',
        ];

        $response = $this->actingAs($this->admin)
            ->post(route('companies.store'), $companyData);

        $response->assertSessionHasErrors('name_translations.en');
    }

    /** @test */
    public function company_slug_is_auto_generated_from_english_name()
    {
        $companyData = [
            'name_translations' => [
                'en' => 'My Test Company',
                'ar' => 'شركتي التجريبية',
            ],
            'email' => 'test@company.com',
            'primary_color' => '#FF0000',
        ];

        $this->actingAs($this->admin)
            ->post(route('companies.store'), $companyData);

        $this->assertDatabaseHas('companies', [
            'email' => 'test@company.com',
            'slug' => 'my-test-company',
        ]);
    }

    /** @test */
    public function company_slug_is_unique()
    {
        $existingCompany = Company::factory()->create([
            'user_id' => $this->admin->id,
            'slug' => 'test-company',
        ]);
        $existingCompany->setTranslations('name', ['en' => 'Test Company']);

        $companyData = [
            'name_translations' => [
                'en' => 'Test Company',
            ],
            'email' => 'new@company.com',
            'primary_color' => '#FF0000',
        ];

        $this->actingAs($this->admin)
            ->post(route('companies.store'), $companyData);

        $newCompany = Company::where('email', 'new@company.com')->first();
        $this->assertNotNull($newCompany);
        $this->assertEquals('test-company-1', $newCompany->slug);
    }

    /** @test */
    public function admin_cannot_access_other_users_company()
    {
        $otherUser = User::factory()->create();
        $company = Company::factory()->create(['user_id' => $otherUser->id]);
        $company->setTranslations('name', ['en' => 'Other Company']);

        $response = $this->actingAs($this->admin)
            ->get(route('companies.details', $company));

        $response->assertStatus(403);
    }

    /** @test */
    public function company_translations_are_stored_correctly()
    {
        $companyData = [
            'name_translations' => [
                'en' => 'English Name',
                'ar' => 'الاسم العربي',
            ],
            'description_translations' => [
                'en' => 'English Description',
                'ar' => 'الوصف العربي',
            ],
            'email' => 'test@company.com',
            'primary_color' => '#FF0000',
        ];

        $this->actingAs($this->admin)
            ->post(route('companies.store'), $companyData);

        $company = Company::where('email', 'test@company.com')->first();
        
        $this->assertEquals('English Name', $company->getTranslatedName('en'));
        $this->assertEquals('الاسم العربي', $company->getTranslatedName('ar'));
        $this->assertEquals('English Description', $company->getTranslatedDescription('en'));
        $this->assertEquals('الوصف العربي', $company->getTranslatedDescription('ar'));
    }

    /** @test */
    public function company_falls_back_to_english_if_arabic_translation_missing()
    {
        $company = Company::factory()->create(['user_id' => $this->admin->id]);
        $company->setTranslations('name', ['en' => 'English Only']);

        app()->setLocale('ar');
        $this->assertEquals('English Only', $company->getTranslatedName());
    }
}

