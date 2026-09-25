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
        Schema::create('juz_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedTinyInteger('juz_number');
            $table->unsignedInteger('total_ayat')->default(0);
            $table->unsignedInteger('ayat_hafal')->default(0);
            $table->decimal('percentage', 5, 2)->default(0.00);
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'mutqin'])->default('not_started');
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->date('mutqin_at')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'juz_number'], 'uq_student_juz');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('juz_progress');
    }
};
