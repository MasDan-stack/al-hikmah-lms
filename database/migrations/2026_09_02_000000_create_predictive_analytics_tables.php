<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Riwayat Prediksi Risiko Dropout Santri
        Schema::create('student_dropout_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('prediction_date');
            $table->decimal('risk_score', 5, 2)->default(0.00);
            $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->decimal('attendance_score', 5, 2)->default(0.00);
            $table->decimal('payment_score', 5, 2)->default(0.00);
            $table->decimal('progress_score', 5, 2)->default(0.00);
            $table->decimal('engagement_score', 5, 2)->default(0.00);
            $table->json('risk_factors')->nullable();
            $table->json('recommendations')->nullable();
            $table->boolean('is_alerted')->default(false);
            $table->timestamp('alerted_at')->nullable();
            $table->timestamps();

            $table->index(['student_id', 'prediction_date'], 'idx_sdp_student_date');
            $table->index(['risk_level', 'prediction_date'], 'idx_sdp_level_date');
        });

        // 2. Analitik Kecepatan Belajar Santri (Learning Velocity)
        Schema::create('student_learning_velocities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('calculation_date');
            $table->decimal('velocity_ayat_per_day', 5, 2)->default(0.00);
            $table->decimal('target_velocity', 5, 2)->default(5.00);
            $table->enum('velocity_status', ['excellent', 'good', 'moderate', 'slow', 'no_data'])->default('no_data');
            $table->decimal('target_achievement_percent', 5, 2)->default(0.00);
            $table->unsignedInteger('days_active')->default(0);
            $table->unsignedInteger('total_ayat_30d')->default(0);
            $table->unsignedInteger('estimated_days_remaining')->nullable();
            $table->date('projected_completion_date')->nullable();
            $table->decimal('percentile_rank', 5, 2)->default(50.00);
            $table->timestamps();

            $table->index(['student_id', 'calculation_date'], 'idx_slv_student_date');
            $table->index('velocity_status', 'idx_slv_status');
        });

        // 3. Proyeksi Pendapatan Bulanan (Revenue Forecasts)
        Schema::create('revenue_forecasts', function (Blueprint $table) {
            $table->id();
            $table->date('forecast_date');
            $table->string('forecast_month', 20); // Format: "2026-09"
            $table->string('forecast_month_label', 50); // Format: "September 2026"
            $table->decimal('predicted_amount', 14, 2)->default(0.00);
            $table->decimal('confidence_level', 5, 2)->default(0.00);
            $table->decimal('trend_value', 12, 2)->default(0.00);
            $table->decimal('seasonal_factor', 5, 2)->default(1.00);
            $table->decimal('estimated_churn_discount', 12, 2)->default(0.00);
            $table->timestamps();

            $table->index('forecast_month', 'idx_rf_month');
            $table->index('forecast_date', 'idx_rf_date');
        });

        // 4. Audit Trail Log untuk Compliance & Intervensi
        Schema::create('predictive_analytics_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('action_type', ['recalculate', 'intervention_wa', 'export', 'view'])->default('view');
            $table->enum('target_type', ['student', 'mentor', 'revenue'])->default('student');
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'action_type', 'created_at'], 'idx_audit_user_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('predictive_analytics_audit_logs');
        Schema::dropIfExists('revenue_forecasts');
        Schema::dropIfExists('student_learning_velocities');
        Schema::dropIfExists('student_dropout_predictions');
    }
};
