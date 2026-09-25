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
        Schema::table('mentor_applications', function (Blueprint $table) {
            $table->timestamp('interview_scheduled_at')->nullable()->after('current_stage');
            $table->string('interview_meeting_link', 255)->nullable()->after('interview_scheduled_at');
            $table->enum('interview_type', ['online', 'offline'])->default('online')->after('interview_meeting_link');
            $table->text('interview_notes')->nullable()->after('interview_type');

            $table->index(['status', 'current_stage']);
            $table->index('created_at');
        });

        Schema::table('mentor_probation_trackings', function (Blueprint $table) {
            $table->foreignId('application_id')->nullable()->after('mentor_id')->constrained('mentor_applications')->nullOnDelete();
            $table->timestamp('last_synced_at')->nullable()->after('active_students_assigned');
            $table->json('orientation_modules_status')->nullable()->after('training_modules_required');

            $table->index(['end_date', 'status']);
        });

        Schema::table('mentor_feedback', function (Blueprint $table) {
            $table->index('session_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentor_feedback', function (Blueprint $table) {
            $table->dropIndex(['session_id']);
        });

        Schema::table('mentor_probation_trackings', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropIndex(['end_date', 'status']);
            $table->dropColumn(['application_id', 'last_synced_at', 'orientation_modules_status']);
        });

        Schema::table('mentor_applications', function (Blueprint $table) {
            $table->dropIndex(['status', 'current_stage']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['interview_scheduled_at', 'interview_meeting_link', 'interview_type', 'interview_notes']);
        });
    }
};
