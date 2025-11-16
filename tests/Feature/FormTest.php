<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Form;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class FormTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);
    }

    /** @test */
    public function admin_can_view_forms_index()
    {
        $response = $this->actingAs($this->admin)->get(route('forms.index'));

        $response->assertStatus(200);
        $response->assertViewIs('forms.index');
    }

    /** @test */
    public function admin_can_create_form()
    {
        $response = $this->actingAs($this->admin)->get(route('forms.create'));

        $response->assertStatus(200);
        $response->assertViewIs('forms.create');
    }

    /** @test */
    public function admin_can_store_form()
    {
        $formData = [
            'title_translations' => [
                'en' => 'Test Form',
                'ar' => 'نموذج تجريبي',
            ],
            'description_translations' => [
                'en' => 'This is a test form',
                'ar' => 'هذا نموذج تجريبي',
            ],
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->admin)->post(route('forms.store'), $formData);

        $response->assertRedirect();
        
        $form = \App\Models\Form::where('user_id', $this->admin->id)->first();
        $this->assertNotNull($form);
        $this->assertEquals('Test Form', $form->getTranslatedTitle('en'));
        $this->assertEquals('نموذج تجريبي', $form->getTranslatedTitle('ar'));
    }

    /** @test */
    public function admin_can_view_form()
    {
        $form = Form::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get(route('forms.show', $form));

        $response->assertStatus(200);
        $response->assertViewIs('forms.show');
    }

    /** @test */
    public function admin_can_edit_form()
    {
        $form = Form::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->get(route('forms.edit', $form));

        $response->assertStatus(200);
        $response->assertViewIs('forms.edit');
    }

    /** @test */
    public function admin_can_update_form()
    {
        $form = Form::factory()->create(['user_id' => $this->admin->id]);
        $form->setTranslations('title', ['en' => 'Original Title']);

        $updateData = [
            'title_translations' => [
                'en' => 'Updated Form Title',
                'ar' => 'عنوان النموذج المحدث',
            ],
            'description_translations' => [
                'en' => 'Updated description',
                'ar' => 'وصف محدث',
            ],
            'status' => 'published',
        ];

        $response = $this->actingAs($this->admin)->put(route('forms.update', $form), $updateData);

        $response->assertRedirect(route('forms.index'));
        $form->refresh();
        $this->assertEquals('Updated Form Title', $form->getTranslatedTitle('en'));
        $this->assertEquals('عنوان النموذج المحدث', $form->getTranslatedTitle('ar'));
    }

    /** @test */
    public function admin_can_delete_form()
    {
        $form = Form::factory()->create(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->admin)->delete(route('forms.destroy', $form));

        $response->assertRedirect(route('forms.index'));
        $this->assertDatabaseMissing('forms', ['id' => $form->id]);
    }

    /** @test */
    public function admin_can_duplicate_form()
    {
        $form = Form::factory()->create(['user_id' => $this->admin->id]);
        $form->setTranslations('title', ['en' => 'Original Form']);

        $response = $this->actingAs($this->admin)->post(route('forms.duplicate', $form));

        $response->assertRedirect();
        
        $duplicatedForm = \App\Models\Form::where('user_id', $this->admin->id)
            ->where('id', '!=', $form->id)
            ->first();
        
        $this->assertNotNull($duplicatedForm);
        $this->assertStringContainsString('(Copy)', $duplicatedForm->getTranslatedTitle('en'));
    }

    /** @test */
    public function admin_can_publish_form()
    {
        $form = Form::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->post(route('forms.publish', $form));

        $response->assertRedirect();
        $this->assertDatabaseHas('forms', [
            'id' => $form->id,
            'status' => 'published',
        ]);
    }

    /** @test */
    public function admin_can_unpublish_form()
    {
        $form = Form::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'published',
        ]);

        $response = $this->actingAs($this->admin)->post(route('forms.unpublish', $form));

        $response->assertRedirect();
        $this->assertDatabaseHas('forms', [
            'id' => $form->id,
            'status' => 'draft',
        ]);
    }

    /** @test */
    public function non_admin_cannot_access_forms()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('forms.index'));

        $response->assertStatus(403);
    }

    /** @test */
    public function guest_can_view_public_form()
    {
        $form = Form::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'published',
            'starts_at' => null,
            'expires_at' => null,
            'require_auth' => false,
        ]);

        $response = $this->get(route('forms.show', $form->slug));

        $response->assertStatus(200);
    }

    /** @test */
    public function guest_cannot_view_draft_form()
    {
        $form = Form::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'draft',
        ]);

        $response = $this->get(route('forms.show', $form));

        $response->assertStatus(404);
    }

    /** @test */
    public function form_requires_english_title()
    {
        $response = $this->actingAs($this->admin)->post(route('forms.store'), [
            'description_translations' => [
                'en' => 'Test description',
            ],
        ]);

        $response->assertSessionHasErrors('title_translations.en');
    }

    /** @test */
    public function form_slug_is_auto_generated_from_english_title()
    {
        $formData = [
            'title_translations' => [
                'en' => 'My Test Form',
                'ar' => 'نموذجي التجريبي',
            ],
            'description_translations' => [
                'en' => 'Test',
            ],
            'status' => 'draft',
        ];

        $response = $this->actingAs($this->admin)->post(route('forms.store'), $formData);
        
        $response->assertRedirect();

        $form = \App\Models\Form::where('user_id', $this->admin->id)->first();
        $this->assertNotNull($form, 'Form was not created. Check validation errors.');
        $this->assertEquals('my-test-form', $form->slug);
        $this->assertEquals('My Test Form', $form->getTranslatedTitle('en'));
    }
}
