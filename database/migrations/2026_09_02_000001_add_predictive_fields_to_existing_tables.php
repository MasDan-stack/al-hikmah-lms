<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Alter Tabel Students
        Schema::table('students', function (Blueprint $table) {
            $table->date('last_dropout_prediction_at')->nullable()->after('privacy_leaderboard');
            $table->string('dropout_risk_level', 20)->nullable()->after('last_dropout_prediction_at');
            $table->decimal('dropout_risk_score', 5, 2)->nullable()->after('dropout_risk_level');
        });

        // Alter Tabel Mentors
        Schema::table('mentors', function (Blueprint $table) {
            $table->date('last_coaching_alert_at')->nullable()->after('rating');
            $table->boolean('coaching_needed')->default(false)->after('last_coaching_alert_at');
            $table->string('coaching_urgency', 20)->nullable()->after('coaching_needed');
        });

        // Index Optimization Tabel Existing
        Schema::table('learning_sessions', function (Blueprint $table) {
            $indexes = Schema::getIndexes('learning_sessions');
            $indexExists = collect($indexes)->contains('name', 'idx_sc_student_date_status');
            if (! $indexExists) {
                $table->index(['student_id', 'date', 'status'], 'idx_sc_student_date_status');
            }
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['student_id', 'status', 'payment_date'], 'idx_pay_student_status_paid');
        });

        Schema::table('progress', function (Blueprint $table) {
            $table->index(['student_id', 'created_at'], 'idx_prog_student_created');
        });
    }

    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->dropIndex('idx_prog_student_created');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_pay_student_status_paid');
        });

        Schema::table('learning_sessions', function (Blueprint $table) {
            $table->dropIndex('idx_sc_student_date_status');
        });

        Schema::table('mentors', function (Blueprint $table) {
            $table->dropColumn(['last_coaching_alert_at', 'coaching_needed', 'coaching_urgency']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['last_dropout_prediction_at', 'dropout_risk_level', 'dropout_risk_score']);
        });
    }
};
