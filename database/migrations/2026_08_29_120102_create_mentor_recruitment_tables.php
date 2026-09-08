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
        Schema::create('mentor_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('application_code', 30)->unique();

            // Data Pribadi
            $table->string('full_name', 150);
            $table->string('email', 150)->index();
            $table->string('phone', 25)->index();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female'])->default('male');
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();

            // Kualifikasi & Sanad
            $table->string('education', 100)->nullable();
            $table->string('institution', 150)->nullable();
            $table->unsignedTinyInteger('experience_years')->default(0);
            $table->text('experience_description')->nullable();
            $table->string('specialization', 50)->default('Tahfidz');
            $table->text('sanad_chain')->nullable();
            $table->unsignedTinyInteger('hifz_total_juz')->default(0);

            // Tahapan Seleksi (Status Workflow)
            $table->enum('status', [
                'submitted',
                'document_review',
                'test_scheduled',
                'test_completed',
                'interview_scheduled',
                'interview_completed',
                'approved',
                'rejected',
                'withdrawn',
            ])->default('submitted')->index();

            $table->unsignedTinyInteger('current_stage')->default(1);
            $table->decimal('final_score', 5, 2)->nullable();
            $table->text('admin_notes')->nullable();
            $table->text('rejection_reason')->nullable();

            // Tracking & Audit
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            // Composite Index
            $table->index(['status', 'submitted_at']);
            $table->index(['specialization', 'status']);
        });

        Schema::create('mentor_application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('mentor_applications')->cascadeOnDelete();
            $table->enum('document_type', ['cv', 'certificate', 'sanad', 'id_card', 'photo', 'other'])->default('cv');
            $table->string('file_path', 255);
            $table->string('file_name', 255);
            $table->unsignedInteger('file_size')->default(0);
            $table->string('mime_type', 100)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'document_type']);
        });

        Schema::create('mentor_test_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained('mentor_applications')->cascadeOnDelete();
            $table->enum('session_type', ['juz_test', 'tajwid_test', 'teaching_simulation', 'interview'])->default('juz_test');
            $table->dateTime('scheduled_at');
            $table->unsignedSmallInteger('duration_minutes')->default(45);
            $table->enum('mode', ['online', 'offline'])->default('online');
            $table->string('meeting_link', 255)->nullable();
            $table->string('location', 255)->nullable();

            // Evaluasi & Skor
            $table->decimal('score', 5, 2)->nullable();
            $table->enum('grade', ['mumtaz', 'jayyid_jiddan', 'jayyid', 'maqbul', 'rasib'])->nullable();
            $table->text('evaluator_notes')->nullable();
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->nullOnDelete();

            // Status Sesi
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled', 'rescheduled'])->default('scheduled');
            $table->timestamp('completed_at')->nullable();
            $table->json('ai_question_payload')->nullable();
            $table->timestamps();

            $table->index(['application_id', 'session_type']);
            $table->index(['scheduled_at', 'status']);
        });

        Schema::table('mentors', function (Blueprint $table) {
            $table->foreignId('application_id')->nullable()->after('user_id')->constrained('mentor_applications')->nullOnDelete();
            $table->date('join_date')->nullable()->after('rating');
            $table->date('probation_end_date')->nullable()->after('join_date');
            $table->enum('status', ['active', 'inactive', 'probation', 'suspended', 'resigned'])->default('active')->after('probation_end_date');
            $table->boolean('is_trainer')->default(false)->after('status');
            $table->text('sanad_chain')->nullable()->after('bio');
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_number', 50)->nullable();
            $table->string('bank_account_name', 150)->nullable();
            $table->string('emergency_contact', 50)->nullable();

            $table->index('status');
            $table->index('join_date');
        });

        Schema::create('mentor_probation_trackings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedTinyInteger('duration_months')->default(3);

            // Checklist Onboarding
            $table->boolean('orientation_completed')->default(false);
            $table->boolean('system_training_completed')->default(false);
            $table->boolean('first_session_conducted')->default(false);
            $table->unsignedTinyInteger('training_modules_completed')->default(0);
            $table->unsignedTinyInteger('training_modules_required')->default(4);

            // Metrik Kinerja Aktual
            $table->unsignedSmallInteger('total_sessions_conducted')->default(0);
            $table->unsignedSmallInteger('active_students_assigned')->default(0);
            $table->decimal('average_rating', 3, 2)->default(5.00);
            $table->decimal('attendance_rate', 5, 2)->default(100.00);

            // Hasil Evaluasi
            $table->date('mid_review_date')->nullable();
            $table->text('mid_review_notes')->nullable();
            $table->date('final_evaluation_date')->nullable();
            $table->enum('final_decision', ['passed', 'extended', 'terminated'])->nullable();
            $table->text('final_notes')->nullable();
            $table->foreignId('evaluated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('status', ['active', 'passed', 'extended', 'terminated'])->default('active')->index();
            $table->timestamps();

            $table->index(['mentor_id', 'status']);
        });

        Schema::create('mentor_trainings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->string('title', 200);
            $table->enum('category', ['pedagogy', 'tajwid', 'tahfidz_method', 'technology', 'adab_counseling'])->default('pedagogy');
            $table->string('instructor_name', 150)->nullable();
            $table->date('training_date');
            $table->decimal('duration_hours', 4, 1)->default(2.0);
            $table->string('certificate_path', 255)->nullable();
            $table->foreignId('badge_id')->nullable()->constrained('badges')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['mentor_id', 'category']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_trainings');
        Schema::dropIfExists('mentor_probation_trackings');

        Schema::table('mentors', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['join_date']);
            $table->dropColumn([
                'application_id', 'join_date', 'probation_end_date', 'status',
                'is_trainer', 'sanad_chain', 'bank_name', 'bank_account_number',
                'bank_account_name', 'emergency_contact',
            ]);
        });

        Schema::dropIfExists('mentor_test_sessions');
        Schema::dropIfExists('mentor_application_documents');
        Schema::dropIfExists('mentor_applications');
    }
};
