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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('topic');
            $table->enum('difficulty', ['Mudah', 'Sedang', 'Sulit'])->default('Sedang');
            $table->text('question');
            $table->json('options');
            $table->unsignedTinyInteger('correct_answer')->comment('0=A, 1=B, 2=C, 3=D');
            $table->text('explanation')->nullable();
            $table->boolean('created_by_ai')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['program_id', 'topic']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
