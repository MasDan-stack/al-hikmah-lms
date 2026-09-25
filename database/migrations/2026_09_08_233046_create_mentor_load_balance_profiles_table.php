<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_load_balance_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->unique()->constrained('mentors')->cascadeOnDelete();
            $table->enum('employment_type', ['part_time', 'full_time', 'volunteer'])->default('part_time');
            $table->unsignedSmallInteger('max_active_students')->default(25);
            $table->unsignedSmallInteger('max_daily_slots')->default(5);
            $table->unsignedSmallInteger('current_active_students')->default(0);
            $table->decimal('burnout_risk_score', 5, 2)->default(0.00); // 0.00 - 100.00%
            $table->enum('burnout_level', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->boolean('is_throttled')->default(false); // Saklar perlindungan overload
            $table->dateTime('coaching_cooldown_until')->nullable();
            $table->string('cooldown_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_load_balance_profiles');
    }
};
