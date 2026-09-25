<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            if (! Schema::hasColumn('mentors', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('full_name');
            }
            if (! Schema::hasColumn('mentors', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (! Schema::hasColumn('mentors', 'education')) {
                $table->string('education', 100)->nullable()->after('city');
            }
            if (! Schema::hasColumn('mentors', 'institution')) {
                $table->string('institution', 150)->nullable()->after('education');
            }
            if (! Schema::hasColumn('mentors', 'experience_years')) {
                $table->unsignedTinyInteger('experience_years')->default(0)->after('institution');
            }
            if (! Schema::hasColumn('mentors', 'hifz_total_juz')) {
                $table->unsignedTinyInteger('hifz_total_juz')->default(0)->after('experience_years');
            }
        });
    }

    public function down(): void
    {
        Schema::table('mentors', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['birth_date', 'city', 'education', 'institution', 'experience_years', 'hifz_total_juz'] as $col) {
                if (Schema::hasColumn('mentors', $col)) {
                    $columnsToDrop[] = $col;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
