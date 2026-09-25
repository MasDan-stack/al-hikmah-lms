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
        // 1. Snapshot Performa Mentor Bulanan / Kuartalan / Tahunan
        Schema::create('mentor_performance_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->enum('period_type', ['monthly', 'quarterly', 'yearly'])->default('monthly');
            $table->date('period_start');
            $table->date('period_end');

            // Metrik Operasional & Akademik
            $table->unsignedInteger('total_students')->default(0);
            $table->unsignedInteger('active_students')->default(0);
            $table->decimal('retention_rate', 5, 2)->default(0.00);
            $table->decimal('dropout_rate', 5, 2)->default(0.00);
            $table->decimal('avg_tajwid_score', 5, 2)->default(0.00);
            $table->decimal('avg_adab_score', 5, 2)->default(0.00);
            $table->decimal('academic_quality_score', 5, 2)->default(0.00);
            $table->unsignedInteger('total_sessions')->default(0);
            $table->unsignedInteger('completed_sessions')->default(0);
            $table->decimal('attendance_rate', 5, 2)->default(0.00);
            $table->decimal('avg_rating_raw', 3, 2)->default(5.00);
            $table->decimal('avg_rating_bayesian', 3, 2)->default(4.50);
            $table->unsignedInteger('total_feedback_count')->default(0);
            $table->decimal('target_achievement_rate', 5, 2)->default(0.00);
            $table->decimal('handicap_bonus_points', 4, 2)->default(0.00);

            // Skor Komposit & Ranking
            $table->decimal('composite_score', 5, 2)->default(0.00);
            $table->unsignedInteger('rank_position')->nullable();
            $table->boolean('is_locked')->default(true);
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['mentor_id', 'period_type', 'period_start', 'period_end'], 'uniq_mentor_perf_snapshot');
            $table->index(['period_type', 'period_start', 'composite_score'], 'idx_perf_score_ranking');
        });

        // 2. Master Ulasan & Rating Mentor dari Wali Santri
        Schema::create('mentor_feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->foreignId('student_id')->nullable()->constrained('students')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('session_id')->nullable()->constrained('learning_sessions')->nullOnDelete();

            $table->unsignedTinyInteger('overall_rating')->default(5); // 1 - 5 Bintang
            $table->text('comment')->nullable();
            $table->json('quick_tags')->nullable(); // Array of strings, e.g. ["#SangatSabar", "#TepatWaktu"]
            $table->boolean('is_anonymous')->default(false);
            $table->text('mentor_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['mentor_id', 'created_at']);
        });

        // 3. Rincian Rating Multi-Kategori Pasca Sesi
        Schema::create('mentor_feedback_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('feedback_id')->constrained('mentor_feedback')->cascadeOnDelete();
            $table->string('category', 50)->default('overall');
            $table->unsignedTinyInteger('rating')->default(5); // 1 - 5 Bintang
            $table->timestamps();
        });

        // 4. AI Prescriptive Insights & Predictive Coaching
        Schema::create('mentor_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->string('period', 20)->default(date('Y-m')); // "2026-08"
            $table->text('ai_summary')->nullable();
            $table->json('coaching_recommendations')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->decimal('predicted_score_next_month', 5, 2)->nullable();
            $table->string('ai_model_used', 50)->default('gemini-2.5-flash');
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->index(['mentor_id', 'period']);
        });

        // 5. Target Capaian Mandiri Guru (Goal Setting & Milestone)
        Schema::create('mentor_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->enum('goal_type', ['rating', 'retention', 'tajwid_score', 'attendance', 'target_completion'])->default('rating');
            $table->string('title');
            $table->decimal('target_value', 5, 2);
            $table->decimal('current_value', 5, 2)->default(0.00);
            $table->string('period', 20)->default(date('Y-m')); // "2026-08"
            $table->string('status', 50)->default('in_progress'); // 'in_progress', 'active', 'achieved', 'missed', 'failed'
            $table->timestamp('achieved_at')->nullable();
            $table->timestamps();
        });

        // 6. Formulir Evaluasi Diri & Refleksi Bulanan Guru
        Schema::create('mentor_self_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->string('period', 20); // "2026-08"
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('action_plan')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            $table->unique(['mentor_id', 'period'], 'uniq_mentor_self_assessment');
        });

        // 7. Data Kelayakan Bonus Finansial & Sertifikat Digital
        Schema::create('mentor_incentives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->enum('incentive_type', ['bonus', 'certificate', 'badge', 'award_nomination'])->default('bonus');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->nullable();
            $table->string('certificate_number')->nullable();
            $table->string('certificate_url')->nullable();
            $table->string('period', 20); // "2026-08"
            $table->timestamp('awarded_at')->nullable();
            $table->timestamps();
        });

        // 8. Pelacakan Metode Ajar Per Sesi (Learning Analytics)
        Schema::create('learning_session_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('learning_sessions')->cascadeOnDelete();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->string('method_used', 100); // Talaqqi, Baghdadi, Iqra Cepat, Drill Tajwid, Storytelling
            $table->unsignedInteger('duration_minutes')->default(45);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_session_methods');
        Schema::dropIfExists('mentor_incentives');
        Schema::dropIfExists('mentor_self_assessments');
        Schema::dropIfExists('mentor_goals');
        Schema::dropIfExists('mentor_insights');
        Schema::dropIfExists('mentor_feedback_ratings');
        Schema::dropIfExists('mentor_feedback');
        Schema::dropIfExists('mentor_performance_snapshots');
    }
};
