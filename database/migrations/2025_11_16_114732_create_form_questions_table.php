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
        Schema::create('form_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('form_id')->constrained()->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('form_sections')->onDelete('set null');
            $table->string('type', 50); // 'short_text', 'long_text', 'multiple_choice', etc.
            $table->text('question_text');
            $table->text('help_text')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->json('settings')->nullable(); // Type-specific settings (max_length, min_value, etc.)
            $table->json('conditional_logic')->nullable(); // Conditional display rules
            $table->timestamps();

            $table->index('form_id');
            $table->index('section_id');
            $table->index('order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_questions');
    }
};
