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
        Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->morphs('translatable'); // translatable_type, translatable_id
            $table->string('locale', 10); // en, ar
            $table->string('field_name'); // title, description, name, etc.
            $table->text('value'); // The translated value
            $table->timestamps();

            // Indexes with custom short names
            $table->index(
                ['translatable_type', 'translatable_id', 'locale', 'field_name'],
                'translatable_full_idx'
            );

            $table->index(
                ['translatable_type', 'translatable_id'],
                'translatable_basic_idx'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('translations');
    }
};
