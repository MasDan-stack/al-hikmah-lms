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
        // 1. Buat tabel student_mutation_logs jika belum ada
        if (! Schema::hasTable('student_mutation_logs')) {
            Schema::create('student_mutation_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('parent_id')->nullable()->constrained('parents')->nullOnDelete();
                $table->foreignId('student_id')->nullable()->constrained('students')->cascadeOnDelete();
                $table->foreignId('previous_mentor_id')->constrained('mentors')->cascadeOnDelete();
                $table->foreignId('new_mentor_id')->nullable()->constrained('mentors')->nullOnDelete();
                $table->string('reason_category')->default('dissatisfaction'); // dissatisfaction, schedule_conflict, relocation, etc.
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // 2. Tambah kolom gender pada tabel mentors jika belum ada
        Schema::table('mentors', function (Blueprint $table) {
            if (! Schema::hasColumn('mentors', 'gender')) {
                $table->enum('gender', ['L', 'P'])->default('L')->after('full_name');
            }
        });

        // 3. Perbarui kolom tabel matching_logs agar presisi sesuai PRD
        Schema::table('matching_logs', function (Blueprint $table) {
            if (! Schema::hasColumn('matching_logs', 'mentor_id') && Schema::hasColumn('matching_logs', 'recommended_mentor_id')) {
                $table->dropForeign(['recommended_mentor_id']);
                $table->renameColumn('recommended_mentor_id', 'mentor_id');
                $table->foreign('mentor_id')->references('id')->on('mentors')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('matching_logs', 'score')) {
                $table->decimal('score', 5, 2)->default(100.0)->after('mentor_id');
            }

            if (! Schema::hasColumn('matching_logs', 'breakdown') && Schema::hasColumn('matching_logs', 'score_breakdown')) {
                $table->renameColumn('score_breakdown', 'breakdown');
            } elseif (! Schema::hasColumn('matching_logs', 'breakdown')) {
                $table->json('breakdown')->nullable()->after('score');
            }

            if (! Schema::hasColumn('matching_logs', 'selection_type')) {
                $table->enum('selection_type', ['recommended', 'auto_high_confidence', 'bulk_assignment', 'manual'])
                    ->default('recommended')
                    ->after('breakdown');
            }

            if (! Schema::hasColumn('matching_logs', 'selected_by')) {
                $table->foreignId('selected_by')->nullable()->after('selection_type')->constrained('users')->nullOnDelete();
            }

            if (Schema::hasColumn('matching_logs', 'student_id')) {
                $table->dropForeign(['student_id']);
                $table->dropColumn('student_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_mutation_logs');

        Schema::table('mentors', function (Blueprint $table) {
            if (Schema::hasColumn('mentors', 'gender')) {
                $table->dropColumn('gender');
            }
        });

        Schema::table('matching_logs', function (Blueprint $table) {
            if (Schema::hasColumn('matching_logs', 'selected_by')) {
                $table->dropForeign(['selected_by']);
                $table->dropColumn(['selected_by', 'selection_type', 'score']);
            }
        });
    }
};
