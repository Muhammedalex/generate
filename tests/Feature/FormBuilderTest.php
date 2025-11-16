<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Form;
use App\Models\FormSection;
use App\Models\FormQuestion;
use App\Models\QuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FormBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Form $form;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create([
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);

        $this->form = Form::factory()->create(['user_id' => $this->admin->id]);
    }

    /** @test */
    public function admin_can_view_form_builder()
    {
        $response = $this->actingAs($this->admin)->get(route('forms.builder', $this->form));

        $response->assertStatus(200);
        $response->assertViewIs('forms.builder');
    }

    /** @test */
    public function admin_can_add_section()
    {
        $sectionData = [
            'title_translations' => [
                'en' => 'New Section',
                'ar' => 'قسم جديد',
            ],
            'description_translations' => [
                'en' => 'Section description',
                'ar' => 'وصف القسم',
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('forms.sections.store', $this->form), $sectionData);

        $response->assertStatus(200);
        
        $section = FormSection::where('form_id', $this->form->id)->first();
        $this->assertNotNull($section);
        $this->assertEquals('New Section', $section->getTranslatedTitle('en'));
        $this->assertEquals('قسم جديد', $section->getTranslatedTitle('ar'));
    }

    /** @test */
    public function admin_can_update_section()
    {
        $section = FormSection::factory()->create(['form_id' => $this->form->id]);
        $section->setTranslations('title', ['en' => 'Original Title']);

        $updateData = [
            'title_translations' => [
                'en' => 'Updated Section Title',
                'ar' => 'عنوان القسم المحدث',
            ],
            'description_translations' => [
                'en' => 'Updated description',
                'ar' => 'وصف محدث',
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->putJson(route('forms.sections.update', [$this->form, $section]), $updateData);

        $response->assertStatus(200);
        $section->refresh();
        $this->assertEquals('Updated Section Title', $section->getTranslatedTitle('en'));
        $this->assertEquals('عنوان القسم المحدث', $section->getTranslatedTitle('ar'));
    }

    /** @test */
    public function admin_can_delete_section()
    {
        $section = FormSection::factory()->create(['form_id' => $this->form->id]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('forms.sections.destroy', [$this->form, $section]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('form_sections', ['id' => $section->id]);
    }

    /** @test */
    public function admin_can_add_question()
    {
        $questionData = [
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'question_text_translations' => [
                'en' => 'What is your name?',
                'ar' => 'ما هو اسمك؟',
            ],
            'is_required' => true,
            'order' => 1,
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('forms.questions.store', $this->form), $questionData);

        $response->assertStatus(200);
        
        $question = FormQuestion::where('form_id', $this->form->id)
            ->where('type', FormQuestion::TYPE_SHORT_TEXT)
            ->first();
        
        $this->assertNotNull($question);
        $this->assertEquals('What is your name?', $question->getTranslatedQuestionText('en'));
        $this->assertEquals('ما هو اسمك؟', $question->getTranslatedQuestionText('ar'));
    }

    /** @test */
    public function admin_can_add_question_with_options()
    {
        $questionData = [
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_MULTIPLE_CHOICE,
            'question_text_translations' => [
                'en' => 'Choose an option',
                'ar' => 'اختر خياراً',
            ],
            'is_required' => true,
            'options' => [
                [
                    'option_text_translations' => [
                        'en' => 'Option 1',
                        'ar' => 'خيار 1',
                    ],
                    'order' => 1
                ],
                [
                    'option_text_translations' => [
                        'en' => 'Option 2',
                        'ar' => 'خيار 2',
                    ],
                    'order' => 2
                ],
                [
                    'option_text_translations' => [
                        'en' => 'Option 3',
                        'ar' => 'خيار 3',
                    ],
                    'order' => 3
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('forms.questions.store', $this->form), $questionData);

        $response->assertStatus(200);
        
        $question = FormQuestion::where('form_id', $this->form->id)
            ->where('type', FormQuestion::TYPE_MULTIPLE_CHOICE)
            ->first();

        $this->assertNotNull($question);
        $this->assertEquals(3, $question->options()->count());
        
        $firstOption = $question->options()->first();
        $this->assertEquals('Option 1', $firstOption->getTranslatedOptionText('en'));
        $this->assertEquals('خيار 1', $firstOption->getTranslatedOptionText('ar'));
    }

    /** @test */
    public function admin_can_update_question()
    {
        $question = FormQuestion::factory()->create(['form_id' => $this->form->id]);
        $question->setTranslations('question_text', ['en' => 'Original question']);

        $updateData = [
            'form_id' => $this->form->id,
            'type' => $question->type,
            'question_text_translations' => [
                'en' => 'Updated question text',
                'ar' => 'نص السؤال المحدث',
            ],
            'is_required' => false,
        ];

        $response = $this->actingAs($this->admin)
            ->putJson(route('forms.questions.update', [$this->form, $question]), $updateData);

        $response->assertStatus(200);
        $question->refresh();
        $this->assertEquals('Updated question text', $question->getTranslatedQuestionText('en'));
        $this->assertEquals('نص السؤال المحدث', $question->getTranslatedQuestionText('ar'));
        $this->assertFalse($question->is_required);
    }

    /** @test */
    public function admin_can_delete_question()
    {
        $question = FormQuestion::factory()->create(['form_id' => $this->form->id]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('forms.questions.destroy', [$this->form, $question]));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('form_questions', ['id' => $question->id]);
    }

    /** @test */
    public function admin_can_duplicate_question()
    {
        $question = FormQuestion::factory()->create(['form_id' => $this->form->id]);
        $question->setTranslations('question_text', ['en' => 'Original Question']);
        QuestionOption::factory()->count(3)->create(['question_id' => $question->id]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('forms.questions.duplicate', [$this->form, $question]));

        $response->assertStatus(200);
        
        $duplicatedQuestion = FormQuestion::where('form_id', $this->form->id)
            ->where('id', '!=', $question->id)
            ->first();

        $this->assertNotNull($duplicatedQuestion);
        $this->assertStringContainsString('(Copy)', $duplicatedQuestion->getTranslatedQuestionText('en'));
        $this->assertEquals(3, $duplicatedQuestion->options()->count());
    }

    /** @test */
    public function admin_can_reorder_questions()
    {
        $question1 = FormQuestion::factory()->create(['form_id' => $this->form->id, 'order' => 1]);
        $question2 = FormQuestion::factory()->create(['form_id' => $this->form->id, 'order' => 2]);
        $question3 = FormQuestion::factory()->create(['form_id' => $this->form->id, 'order' => 3]);

        $reorderData = [
            'questions' => [$question3->id, $question1->id, $question2->id],
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('forms.questions.reorder', $this->form), $reorderData);

        $response->assertStatus(200);
        
        $this->assertEquals(1, $question3->fresh()->order);
        $this->assertEquals(2, $question1->fresh()->order);
        $this->assertEquals(3, $question2->fresh()->order);
    }

    /** @test */
    public function admin_can_reorder_sections()
    {
        $section1 = FormSection::factory()->create(['form_id' => $this->form->id, 'order' => 1]);
        $section2 = FormSection::factory()->create(['form_id' => $this->form->id, 'order' => 2]);

        $reorderData = [
            'sections' => [$section2->id, $section1->id],
        ];

        $response = $this->actingAs($this->admin)
            ->postJson(route('forms.sections.reorder', $this->form), $reorderData);

        $response->assertStatus(200);
        
        $this->assertEquals(1, $section2->fresh()->order);
        $this->assertEquals(2, $section1->fresh()->order);
    }

    /** @test */
    public function non_admin_cannot_access_form_builder()
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('forms.builder', $this->form));

        $response->assertStatus(403);
    }

    /** @test */
    public function question_options_are_deleted_when_question_is_deleted()
    {
        $question = FormQuestion::factory()->create(['form_id' => $this->form->id]);
        $option = QuestionOption::factory()->create(['question_id' => $question->id]);

        $this->actingAs($this->admin)
            ->deleteJson(route('forms.questions.destroy', [$this->form, $question]));

        $this->assertDatabaseMissing('question_options', ['id' => $option->id]);
    }
}
