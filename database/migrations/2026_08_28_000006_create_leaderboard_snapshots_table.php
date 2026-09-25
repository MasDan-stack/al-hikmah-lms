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
        Schema::create('leaderboard_snapshots', function (Blueprint $table) {
            $table->id();
            $table->enum('period_type', ['daily', 'weekly', 'monthly'])->default('weekly');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('category', ['overall', 'anak', 'dewasa', 'per_juz', 'streak'])->default('overall');
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->unsignedInteger('rank_position');
            $table->unsignedInteger('total_points')->default(0);
            $table->unsignedInteger('total_ayat')->default(0);
            $table->unsignedTinyInteger('total_juz_mutqin')->default(0);
            $table->unsignedInteger('current_streak')->default(0);
            $table->enum('trend', ['up', 'down', 'stable'])->default('stable');
            $table->timestamps();

            $table->index(['period_type', 'period_start', 'category'], 'idx_period_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leaderboard_snapshots');
    }
};
