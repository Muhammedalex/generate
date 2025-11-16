<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Form;
use App\Models\FormQuestion;
use App\Models\FormResponse;
use App\Models\ResponseAnswer;
use App\Models\QuestionOption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FormResponseTest extends TestCase
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

        $this->form = Form::factory()->create([
            'user_id' => $this->admin->id,
            'status' => 'published',
            'collect_email' => true,
            'require_auth' => false,
            'starts_at' => now()->subDay(),
            'expires_at' => now()->addDay(),
        ]);
    }

    /** @test */
    public function guest_can_submit_form_response()
    {
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => 'Test answer',
            ],
        ];

        $response = $this->post(route('forms.submit', $this->form), $responseData);

        $response->assertRedirect();
        $this->assertDatabaseHas('form_responses', [
            'form_id' => $this->form->id,
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function authenticated_user_can_submit_form_response()
    {
        $user = User::factory()->create();
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => 'Test answer',
            ],
        ];

        $response = $this->actingAs($user)->post(route('forms.submit', $this->form), $responseData);

        $response->assertRedirect();
        $this->assertDatabaseHas('form_responses', [
            'form_id' => $this->form->id,
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function form_response_validates_required_questions()
    {
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [],
        ];

        $response = $this->post(route('forms.submit', $this->form), $responseData);

        $response->assertSessionHasErrors("answers.{$question->id}");
    }

    /** @test */
    public function form_response_validates_email_when_collect_email_enabled()
    {
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'invalid-email',
            'answers' => [
                $question->id => 'Test answer',
            ],
        ];

        $response = $this->post(route('forms.submit', $this->form), $responseData);

        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function form_response_saves_text_answer()
    {
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => 'My answer text',
            ],
        ];

        $this->post(route('forms.submit', $this->form), $responseData);

        $formResponse = FormResponse::where('form_id', $this->form->id)->first();
        $answer = ResponseAnswer::where('response_id', $formResponse->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertEquals('My answer text', $answer->answer_text);
    }

    /** @test */
    public function form_response_saves_multiple_choice_answer()
    {
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_MULTIPLE_CHOICE,
            'is_required' => true,
        ]);

        $option = QuestionOption::factory()->create(['question_id' => $question->id]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => $option->id,
            ],
        ];

        $this->post(route('forms.submit', $this->form), $responseData);

        $formResponse = FormResponse::where('form_id', $this->form->id)->first();
        $answer = ResponseAnswer::where('response_id', $formResponse->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertEquals($option->id, $answer->answer_text);
    }

    /** @test */
    public function form_response_saves_checkbox_answers()
    {
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_CHECKBOX,
            'is_required' => false,
        ]);

        $option1 = QuestionOption::factory()->create(['question_id' => $question->id]);
        $option2 = QuestionOption::factory()->create(['question_id' => $question->id]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => [$option1->id, $option2->id],
            ],
        ];

        $this->post(route('forms.submit', $this->form), $responseData);

        $formResponse = FormResponse::where('form_id', $this->form->id)->first();
        $answer = ResponseAnswer::where('response_id', $formResponse->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertIsArray($answer->answer_json);
        $this->assertContains($option1->id, $answer->answer_json);
        $this->assertContains($option2->id, $answer->answer_json);
    }

    /** @test */
    public function form_response_saves_number_answer()
    {
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_NUMBER,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => 42,
            ],
        ];

        $this->post(route('forms.submit', $this->form), $responseData);

        $formResponse = FormResponse::where('form_id', $this->form->id)->first();
        $answer = ResponseAnswer::where('response_id', $formResponse->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertEquals(42, $answer->answer_number);
    }

    /** @test */
    public function form_response_saves_yes_no_answer()
    {
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_YES_NO,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => '1',
            ],
        ];

        $this->post(route('forms.submit', $this->form), $responseData);

        $formResponse = FormResponse::where('form_id', $this->form->id)->first();
        $answer = ResponseAnswer::where('response_id', $formResponse->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertTrue($answer->answer_boolean);
    }

    /** @test */
    public function form_response_saves_file_upload()
    {
        Storage::fake('public');

        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_FILE_UPLOAD,
            'is_required' => true,
        ]);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => $file,
            ],
        ];

        $this->post(route('forms.submit', $this->form), $responseData);

        $formResponse = FormResponse::where('form_id', $this->form->id)->first();
        $answer = ResponseAnswer::where('response_id', $formResponse->id)
            ->where('question_id', $question->id)
            ->first();

        $this->assertNotNull($answer);
        $this->assertNotNull($answer->file_path);
        Storage::disk('public')->assertExists($answer->file_path);
    }

    /** @test */
    public function admin_can_view_form_responses()
    {
        $formResponse = FormResponse::factory()->create(['form_id' => $this->form->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('forms.responses', $this->form));

        $response->assertStatus(200);
        $response->assertViewIs('forms.responses');
    }

    /** @test */
    public function admin_can_view_single_response()
    {
        $formResponse = FormResponse::factory()->create(['form_id' => $this->form->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('forms.responses.show', [$this->form, $formResponse]));

        $response->assertStatus(200);
        $response->assertViewIs('forms.response-detail');
    }

    /** @test */
    public function admin_can_delete_response()
    {
        $formResponse = FormResponse::factory()->create(['form_id' => $this->form->id]);

        $response = $this->actingAs($this->admin)
            ->delete(route('forms.responses.destroy', [$this->form, $formResponse]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('form_responses', ['id' => $formResponse->id]);
    }

    /** @test */
    public function admin_can_export_responses_to_csv()
    {
        $question = FormQuestion::factory()->create(['form_id' => $this->form->id]);
        $formResponse = FormResponse::factory()->create(['form_id' => $this->form->id]);
        ResponseAnswer::factory()->create([
            'response_id' => $formResponse->id,
            'question_id' => $question->id,
            'answer_text' => 'Test answer',
        ]);

        $response = $this->actingAs($this->admin)
            ->get(route('forms.responses.export', $this->form));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
    }

    /** @test */
    public function form_prevents_multiple_submissions_when_not_allowed()
    {
        $this->form->update(['allow_multiple' => false]);
        $user = User::factory()->create();
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => 'First answer',
            ],
        ];

        // First submission
        $this->actingAs($user)->post(route('forms.submit', $this->form), $responseData);

        // Second submission should fail
        $response = $this->actingAs($user)->post(route('forms.submit', $this->form), $responseData);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function form_allows_multiple_submissions_when_enabled()
    {
        $this->form->update(['allow_multiple' => true]);
        $user = User::factory()->create();
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => 'First answer',
            ],
        ];

        // First submission
        $this->actingAs($user)->post(route('forms.submit', $this->form), $responseData);

        // Second submission should succeed
        $response = $this->actingAs($user)->post(route('forms.submit', $this->form), $responseData);

        $response->assertRedirect();
        $this->assertEquals(2, FormResponse::where('form_id', $this->form->id)->count());
    }

    /** @test */
    public function form_requires_authentication_when_enabled()
    {
        $this->form->update(['require_auth' => true]);
        $question = FormQuestion::factory()->create([
            'form_id' => $this->form->id,
            'type' => FormQuestion::TYPE_SHORT_TEXT,
            'is_required' => true,
        ]);

        $responseData = [
            'email' => 'test@example.com',
            'answers' => [
                $question->id => 'Test answer',
            ],
        ];

        $response = $this->post(route('forms.submit', $this->form), $responseData);

        $response->assertRedirect(route('login'));
    }
}
