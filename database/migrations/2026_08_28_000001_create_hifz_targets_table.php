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
        Schema::create('hifz_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('mentor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('learning_session_id')->nullable()->constrained('learning_sessions')->nullOnDelete();
            $table->date('target_date');
            $table->string('surah_name', 100)->nullable();
            $table->unsignedInteger('start_ayat')->default(1);
            $table->unsignedInteger('end_ayat')->default(1);
            $table->unsignedInteger('total_ayat')->default(1);
            $table->text('notes')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'missed'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'target_date'], 'idx_student_date');
            $table->index('status', 'idx_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hifz_targets');
    }
};
