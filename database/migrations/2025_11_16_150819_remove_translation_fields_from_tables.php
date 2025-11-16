<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove translation JSON columns and original fields (moved to translations table)
        
        // Companies table - remove name, description and their translations
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'name_translations', 'description_translations']);
        });

        // Forms table - remove title, description, thank_you_message and their translations
        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn([
                'title', 
                'description', 
                'thank_you_message',
                'title_translations', 
                'description_translations', 
                'thank_you_message_translations'
            ]);
        });

        // Form questions table - remove question_text, help_text and their translations
        Schema::table('form_questions', function (Blueprint $table) {
            $table->dropColumn([
                'question_text',
                'help_text',
                'question_text_translations', 
                'help_text_translations'
            ]);
        });

        // Question options table - remove option_text and its translations
        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn(['option_text', 'option_text_translations']);
        });

        // Form sections table - remove title, description and their translations
        Schema::table('form_sections', function (Blueprint $table) {
            $table->dropColumn([
                'title',
                'description',
                'title_translations', 
                'description_translations'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Companies
        Schema::table('companies', function (Blueprint $table) {
            $table->string('name')->after('user_id');
            $table->text('description')->nullable()->after('name');
            $table->json('name_translations')->nullable()->after('name');
            $table->json('description_translations')->nullable()->after('description');
        });

        // Forms
        Schema::table('forms', function (Blueprint $table) {
            $table->string('title')->after('user_id');
            $table->text('description')->nullable()->after('title');
            $table->text('thank_you_message')->nullable()->after('redirect_url');
            $table->json('title_translations')->nullable()->after('title');
            $table->json('description_translations')->nullable()->after('description');
            $table->json('thank_you_message_translations')->nullable()->after('thank_you_message');
        });

        // Form questions
        Schema::table('form_questions', function (Blueprint $table) {
            $table->text('question_text')->after('type');
            $table->text('help_text')->nullable()->after('question_text');
            $table->json('question_text_translations')->nullable()->after('question_text');
            $table->json('help_text_translations')->nullable()->after('help_text');
        });

        // Question options
        Schema::table('question_options', function (Blueprint $table) {
            $table->string('option_text')->after('question_id');
            $table->json('option_text_translations')->nullable()->after('option_text');
        });

        // Form sections
        Schema::table('form_sections', function (Blueprint $table) {
            $table->string('title')->after('form_id');
            $table->text('description')->nullable()->after('title');
            $table->json('title_translations')->nullable()->after('title');
            $table->json('description_translations')->nullable()->after('description');
        });
    }
};
