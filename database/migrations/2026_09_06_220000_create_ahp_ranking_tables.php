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
        Schema::create('ahp_criteria_configs', function (Blueprint $table) {
            $table->id();
            $table->string('criteria_key', 50)->unique();
            $table->string('criteria_name');
            $table->text('description')->nullable();
            $table->decimal('weight', 6, 4)->default(0.2000);
            $table->json('pairwise_values')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ahp_evaluation_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('period_month', 7)->index(); // YYYY-MM
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->decimal('c1_discipline_score', 6, 2)->default(0.00);
            $table->decimal('c2_pedagogy_score', 6, 2)->default(0.00);
            $table->decimal('c3_morals_score', 6, 2)->default(0.00);
            $table->decimal('c4_satisfaction_score', 6, 2)->default(0.00);
            $table->decimal('c5_involvement_score', 6, 2)->default(0.00);
            $table->decimal('final_ahp_score', 6, 2)->default(0.00);
            $table->unsignedInteger('rank_position')->default(1);
            $table->decimal('reward_amount', 12, 2)->default(0.00);
            $table->text('admin_notes')->nullable();
            $table->boolean('is_announced')->default(false);
            $table->timestamp('announced_at')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['period_month', 'mentor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahp_evaluation_snapshots');
        Schema::dropIfExists('ahp_criteria_configs');
    }
};
