<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentor_student', function (Blueprint $table) {
            $table->index(['mentor_id', 'day_assigned', 'is_active'], 'idx_mentor_day_active');
            $table->index(['student_id', 'day_assigned', 'is_active'], 'idx_student_day_active');
        });
    }

    public function down(): void
    {
        Schema::table('mentor_student', function (Blueprint $table) {
            $table->dropIndex('idx_mentor_day_active');
            $table->dropIndex('idx_student_day_active');
        });
    }
};
