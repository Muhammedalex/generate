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
        Schema::create('response_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('response_id')->constrained('form_responses')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('form_questions')->onDelete('cascade');
            $table->text('answer_text')->nullable();
            $table->decimal('answer_number', 15, 4)->nullable();
            $table->boolean('answer_boolean')->nullable();
            $table->date('answer_date')->nullable();
            $table->json('answer_json')->nullable(); // For complex answers (arrays, objects)
            $table->string('file_path')->nullable(); // For file uploads
            $table->timestamps();

            $table->index('response_id');
            $table->index('question_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('response_answers');
    }
};
