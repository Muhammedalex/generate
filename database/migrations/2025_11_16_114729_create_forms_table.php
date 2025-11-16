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
        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->json('settings')->nullable(); // Additional settings
            $table->json('appearance')->nullable(); // Theme, colors, logo
            $table->boolean('allow_multiple')->default(false);
            $table->boolean('require_auth')->default(false);
            $table->boolean('collect_email')->default(false);
            $table->boolean('show_progress')->default(true);
            $table->boolean('randomize_questions')->default(false);
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->text('thank_you_message')->nullable();
            $table->string('redirect_url')->nullable();
            $table->timestamps();

            $table->index('slug');
            $table->index('user_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forms');
    }
};
