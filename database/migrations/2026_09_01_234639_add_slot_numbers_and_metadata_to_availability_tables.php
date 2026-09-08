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
        Schema::table('mentor_availabilities', function (Blueprint $table) {
            if (! Schema::hasColumn('mentor_availabilities', 'slot_numbers')) {
                $table->json('slot_numbers')->nullable()->after('day');
            }
        });

        Schema::table('mentor_student', function (Blueprint $table) {
            if (! Schema::hasColumn('mentor_student', 'program_id')) {
                $table->foreignId('program_id')->nullable()->after('student_id')->constrained('programs')->nullOnDelete();
            }
            if (! Schema::hasColumn('mentor_student', 'slot_number')) {
                $table->unsignedTinyInteger('slot_number')->nullable()->after('day_assigned');
            }
            if (! Schema::hasColumn('mentor_student', 'time_label')) {
                $table->string('time_label', 50)->nullable()->after('slot_number');
            }
            if (! Schema::hasColumn('mentor_student', 'notes')) {
                $table->text('notes')->nullable()->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentor_availabilities', function (Blueprint $table) {
            if (Schema::hasColumn('mentor_availabilities', 'slot_numbers')) {
                $table->dropColumn('slot_numbers');
            }
        });

        Schema::table('mentor_student', function (Blueprint $table) {
            if (Schema::hasColumn('mentor_student', 'program_id')) {
                $table->dropForeign(['program_id']);
                $table->dropColumn('program_id');
            }
            if (Schema::hasColumn('mentor_student', 'slot_number')) {
                $table->dropColumn('slot_number');
            }
            if (Schema::hasColumn('mentor_student', 'time_label')) {
                $table->dropColumn('time_label');
            }
            if (Schema::hasColumn('mentor_student', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
