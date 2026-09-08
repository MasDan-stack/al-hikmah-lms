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
        Schema::table('mentors', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('bio');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->json('specializations')->nullable()->after('longitude');
            $table->json('blocked_programs')->nullable()->after('specializations');
            $table->json('blocked_days')->nullable()->after('blocked_programs');
            $table->integer('max_students_per_day')->nullable()->after('default_max_students_per_day');
            $table->integer('students_count')->default(0)->after('max_students_per_day');
        });

        Schema::table('students', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('location');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('shadow_mentor_id')->nullable()->after('mentor_id')->constrained('mentors')->nullOnDelete();
            $table->decimal('matching_score', 5, 2)->nullable()->after('shadow_mentor_id');
        });

        Schema::create('matching_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recommended_mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->json('score_breakdown');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matching_logs');

        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropForeign(['shadow_mentor_id']);
            $table->dropColumn(['shadow_mentor_id', 'matching_score']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });

        Schema::table('mentors', function (Blueprint $table) {
            $table->dropColumn([
                'latitude',
                'longitude',
                'specializations',
                'blocked_programs',
                'blocked_days',
                'max_students_per_day',
                'students_count',
            ]);
        });
    }
};
