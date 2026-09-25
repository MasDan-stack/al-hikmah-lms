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
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', ['multiple_choice', 'essay'])->default('multiple_choice')->after('difficulty');
            $table->json('options')->nullable()->change();
            $table->unsignedTinyInteger('correct_answer')->nullable()->change();
            $table->text('essay_answer')->nullable()->after('correct_answer');
            $table->text('rubric')->nullable()->after('essay_answer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'essay_answer', 'rubric']);
            $table->json('options')->nullable(false)->change();
            $table->unsignedTinyInteger('correct_answer')->nullable(false)->change();
        });
    }
};
