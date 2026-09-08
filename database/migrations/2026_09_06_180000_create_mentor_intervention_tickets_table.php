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
        if (! Schema::hasTable('mentor_intervention_tickets')) {
            Schema::create('mentor_intervention_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('ticket_number', 50)->unique();
                $table->foreignId('feedback_id')->nullable()->constrained('mentor_feedback')->cascadeOnDelete();
                $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
                $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
                $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('session_id')->nullable()->constrained('learning_sessions')->nullOnDelete();

                $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->string('complaint_category', 50)->default('dissatisfaction'); // attendance_late, attitude_pedagogy, dissatisfaction, other
                $table->string('sentiment_label', 20)->default('negative');
                $table->text('parent_comment')->nullable();
                $table->json('detected_keywords')->nullable();

                $table->enum('status', ['open', 'in_progress', 'resolved', 'escalated_to_mutation'])->default('open');
                $table->text('action_plan')->nullable();
                $table->text('resolution_notes')->nullable();

                $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->index(['status', 'created_at']);
                $table->index('mentor_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_intervention_tickets');
    }
};
