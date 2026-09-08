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
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedInteger('total_points')->default(0)->after('notes');
            $table->unsignedInteger('current_streak')->default(0)->after('total_points');
            $table->unsignedInteger('longest_streak')->default(0)->after('current_streak');
            $table->date('last_setoran_date')->nullable()->after('longest_streak');
            $table->boolean('privacy_leaderboard')->default(false)->after('last_setoran_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'total_points',
                'current_streak',
                'longest_streak',
                'last_setoran_date',
                'privacy_leaderboard',
            ]);
        });
    }
};
