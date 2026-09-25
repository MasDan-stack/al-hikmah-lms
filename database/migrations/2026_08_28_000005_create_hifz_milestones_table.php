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
        Schema::create('hifz_milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('mentor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name', 150);
            $table->enum('target_type', ['juz_completion', 'ayat_milestone', 'exam', 'custom'])->default('juz_completion');
            $table->dateTime('target_date');
            $table->unsignedInteger('progress_current')->default(0);
            $table->unsignedInteger('progress_goal')->default(1);
            $table->enum('status', ['active', 'achieved', 'expired', 'cancelled'])->default('active');
            $table->timestamp('achieved_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'status'], 'idx_student_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hifz_milestones');
    }
};
