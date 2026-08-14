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
        Schema::table('mentor_student', function (Blueprint $table) {
            $table->enum('day_assigned', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])
                ->nullable()->after('student_id');
            $table->time('time_assigned')->nullable()->after('day_assigned');
            $table->boolean('is_active')->default(true)->after('time_assigned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentor_student', function (Blueprint $table) {
            $table->dropColumn(['day_assigned', 'time_assigned', 'is_active']);
        });
    }
};
