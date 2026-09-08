<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Profil Gaya Belajar Santri
        Schema::create('student_learning_styles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->unique()->constrained('students')->cascadeOnDelete();
            $table->tinyInteger('visual_score')->default(5); // Skala 1 - 10
            $table->tinyInteger('auditory_score')->default(5);
            $table->tinyInteger('kinesthetic_score')->default(5);
            $table->tinyInteger('patience_need')->default(5); // Kebutuhan kesabaran guru
            $table->enum('pace_preference', ['slow_repetitive', 'moderate', 'fast_paced'])->default('moderate');
            $table->string('dominant_style', 30)->default('auditory');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Profil Karakteristik Pedagogis Guru
        Schema::create('mentor_pedagogical_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->unique()->constrained('mentors')->cascadeOnDelete();
            $table->tinyInteger('visual_capability')->default(7);
            $table->tinyInteger('auditory_capability')->default(8);
            $table->tinyInteger('kinesthetic_capability')->default(6);
            $table->tinyInteger('patience_rating')->default(8);
            $table->enum('energy_level', ['calm_soothing', 'balanced', 'energetic_expressive'])->default('balanced');
            $table->enum('preferred_age_group', ['early_childhood', 'primary', 'teen_adult', 'all'])->default('all');
            $table->decimal('historical_retention_rate', 5, 2)->default(90.00); // Persentase retensi
            $table->timestamps();
        });

        // 3. Rekam Jejak Kecocokan Historis
        Schema::create('mentor_student_match_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('enrollment_id')->constrained('enrollments')->cascadeOnDelete();
            $table->decimal('initial_match_score', 5, 2);
            $table->unsignedSmallInteger('retention_weeks')->default(0);
            $table->decimal('average_rating_received', 3, 2)->nullable();
            $table->boolean('is_completed_successfully')->default(false);
            $table->boolean('requested_mutation')->default(false);
            $table->string('mutation_reason')->nullable();
            $table->timestamps();

            $table->index(['mentor_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_student_match_histories');
        Schema::dropIfExists('mentor_pedagogical_profiles');
        Schema::dropIfExists('student_learning_styles');
    }
};
