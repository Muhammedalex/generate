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
        // Add translations to companies table
        Schema::table('companies', function (Blueprint $table) {
            $table->json('name_translations')->nullable()->after('name'); // {"en": "Company Name", "ar": "اسم الشركة"}
            $table->json('description_translations')->nullable()->after('description'); // {"en": "Description", "ar": "الوصف"}
        });

        // Add translations to forms table
        Schema::table('forms', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title'); // {"en": "Form Title", "ar": "عنوان النموذج"}
            $table->json('description_translations')->nullable()->after('description'); // {"en": "Description", "ar": "الوصف"}
            $table->json('thank_you_message_translations')->nullable()->after('thank_you_message'); // {"en": "Thank you", "ar": "شكراً لك"}
        });

        // Add translations to form_questions table
        Schema::table('form_questions', function (Blueprint $table) {
            $table->json('question_text_translations')->nullable()->after('question_text'); // {"en": "Question", "ar": "السؤال"}
            $table->json('help_text_translations')->nullable()->after('help_text'); // {"en": "Help text", "ar": "نص المساعدة"}
        });

        // Add translations to question_options table
        Schema::table('question_options', function (Blueprint $table) {
            $table->json('option_text_translations')->nullable()->after('option_text'); // {"en": "Option", "ar": "الخيار"}
        });

        // Add translations to form_sections table
        Schema::table('form_sections', function (Blueprint $table) {
            $table->json('title_translations')->nullable()->after('title'); // {"en": "Section Title", "ar": "عنوان القسم"}
            $table->json('description_translations')->nullable()->after('description'); // {"en": "Description", "ar": "الوصف"}
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['name_translations', 'description_translations']);
        });

        Schema::table('forms', function (Blueprint $table) {
            $table->dropColumn(['title_translations', 'description_translations', 'thank_you_message_translations']);
        });

        Schema::table('form_questions', function (Blueprint $table) {
            $table->dropColumn(['question_text_translations', 'help_text_translations']);
        });

        Schema::table('question_options', function (Blueprint $table) {
            $table->dropColumn('option_text_translations');
        });

        Schema::table('form_sections', function (Blueprint $table) {
            $table->dropColumn(['title_translations', 'description_translations']);
        });
    }
};
