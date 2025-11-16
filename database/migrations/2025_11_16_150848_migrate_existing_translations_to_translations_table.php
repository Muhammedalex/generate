<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Translation;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This migration migrates existing translation data from JSON columns
     * to the new translations table before removing the old columns.
     */
    public function up(): void
    {
        // Migrate Companies translations
        $companies = DB::table('companies')->get();
        foreach ($companies as $company) {
            // Migrate name
            if ($company->name) {
                Translation::create([
                    'translatable_type' => 'App\Models\Company',
                    'translatable_id' => $company->id,
                    'locale' => 'en',
                    'field_name' => 'name',
                    'value' => $company->name,
                ]);
            }
            
            if ($company->name_translations) {
                $translations = is_string($company->name_translations) 
                    ? json_decode($company->name_translations, true) 
                    : $company->name_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\Company',
                                    'translatable_id' => $company->id,
                                    'locale' => $locale,
                                    'field_name' => 'name',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
            
            // Migrate description
            if ($company->description) {
                Translation::create([
                    'translatable_type' => 'App\Models\Company',
                    'translatable_id' => $company->id,
                    'locale' => 'en',
                    'field_name' => 'description',
                    'value' => $company->description ?? '',
                ]);
            }
            
            if ($company->description_translations) {
                $translations = is_string($company->description_translations) 
                    ? json_decode($company->description_translations, true) 
                    : $company->description_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\Company',
                                    'translatable_id' => $company->id,
                                    'locale' => $locale,
                                    'field_name' => 'description',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
        }

        // Migrate Forms translations
        $forms = DB::table('forms')->get();
        foreach ($forms as $form) {
            // Migrate title
            if ($form->title) {
                Translation::create([
                    'translatable_type' => 'App\Models\Form',
                    'translatable_id' => $form->id,
                    'locale' => 'en',
                    'field_name' => 'title',
                    'value' => $form->title,
                ]);
            }
            
            if ($form->title_translations) {
                $translations = is_string($form->title_translations) 
                    ? json_decode($form->title_translations, true) 
                    : $form->title_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\Form',
                                    'translatable_id' => $form->id,
                                    'locale' => $locale,
                                    'field_name' => 'title',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
            
            // Migrate description
            if ($form->description) {
                Translation::create([
                    'translatable_type' => 'App\Models\Form',
                    'translatable_id' => $form->id,
                    'locale' => 'en',
                    'field_name' => 'description',
                    'value' => $form->description ?? '',
                ]);
            }
            
            if ($form->description_translations) {
                $translations = is_string($form->description_translations) 
                    ? json_decode($form->description_translations, true) 
                    : $form->description_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\Form',
                                    'translatable_id' => $form->id,
                                    'locale' => $locale,
                                    'field_name' => 'description',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
            
            // Migrate thank_you_message
            if ($form->thank_you_message) {
                Translation::create([
                    'translatable_type' => 'App\Models\Form',
                    'translatable_id' => $form->id,
                    'locale' => 'en',
                    'field_name' => 'thank_you_message',
                    'value' => $form->thank_you_message ?? '',
                ]);
            }
            
            if ($form->thank_you_message_translations) {
                $translations = is_string($form->thank_you_message_translations) 
                    ? json_decode($form->thank_you_message_translations, true) 
                    : $form->thank_you_message_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\Form',
                                    'translatable_id' => $form->id,
                                    'locale' => $locale,
                                    'field_name' => 'thank_you_message',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
        }

        // Migrate Form Questions translations
        $questions = DB::table('form_questions')->get();
        foreach ($questions as $question) {
            // Migrate question_text
            if ($question->question_text) {
                Translation::create([
                    'translatable_type' => 'App\Models\FormQuestion',
                    'translatable_id' => $question->id,
                    'locale' => 'en',
                    'field_name' => 'question_text',
                    'value' => $question->question_text,
                ]);
            }
            
            if ($question->question_text_translations) {
                $translations = is_string($question->question_text_translations) 
                    ? json_decode($question->question_text_translations, true) 
                    : $question->question_text_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\FormQuestion',
                                    'translatable_id' => $question->id,
                                    'locale' => $locale,
                                    'field_name' => 'question_text',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
            
            // Migrate help_text
            if ($question->help_text) {
                Translation::create([
                    'translatable_type' => 'App\Models\FormQuestion',
                    'translatable_id' => $question->id,
                    'locale' => 'en',
                    'field_name' => 'help_text',
                    'value' => $question->help_text ?? '',
                ]);
            }
            
            if ($question->help_text_translations) {
                $translations = is_string($question->help_text_translations) 
                    ? json_decode($question->help_text_translations, true) 
                    : $question->help_text_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\FormQuestion',
                                    'translatable_id' => $question->id,
                                    'locale' => $locale,
                                    'field_name' => 'help_text',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
        }

        // Migrate Question Options translations
        $options = DB::table('question_options')->get();
        foreach ($options as $option) {
            // Migrate option_text
            if ($option->option_text) {
                Translation::create([
                    'translatable_type' => 'App\Models\QuestionOption',
                    'translatable_id' => $option->id,
                    'locale' => 'en',
                    'field_name' => 'option_text',
                    'value' => $option->option_text,
                ]);
            }
            
            if ($option->option_text_translations) {
                $translations = is_string($option->option_text_translations) 
                    ? json_decode($option->option_text_translations, true) 
                    : $option->option_text_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\QuestionOption',
                                    'translatable_id' => $option->id,
                                    'locale' => $locale,
                                    'field_name' => 'option_text',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
        }

        // Migrate Form Sections translations
        $sections = DB::table('form_sections')->get();
        foreach ($sections as $section) {
            // Migrate title
            if ($section->title) {
                Translation::create([
                    'translatable_type' => 'App\Models\FormSection',
                    'translatable_id' => $section->id,
                    'locale' => 'en',
                    'field_name' => 'title',
                    'value' => $section->title ?? '',
                ]);
            }
            
            if ($section->title_translations) {
                $translations = is_string($section->title_translations) 
                    ? json_decode($section->title_translations, true) 
                    : $section->title_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\FormSection',
                                    'translatable_id' => $section->id,
                                    'locale' => $locale,
                                    'field_name' => 'title',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
            
            // Migrate description
            if ($section->description) {
                Translation::create([
                    'translatable_type' => 'App\Models\FormSection',
                    'translatable_id' => $section->id,
                    'locale' => 'en',
                    'field_name' => 'description',
                    'value' => $section->description ?? '',
                ]);
            }
            
            if ($section->description_translations) {
                $translations = is_string($section->description_translations) 
                    ? json_decode($section->description_translations, true) 
                    : $section->description_translations;
                
                if (is_array($translations)) {
                    foreach ($translations as $locale => $value) {
                        if (!empty($value)) {
                            Translation::updateOrCreate(
                                [
                                    'translatable_type' => 'App\Models\FormSection',
                                    'translatable_id' => $section->id,
                                    'locale' => $locale,
                                    'field_name' => 'description',
                                ],
                                ['value' => $value]
                            );
                        }
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration only migrates data, so down() doesn't need to do anything
        // The data will be lost when columns are removed, but that's expected
    }
};
