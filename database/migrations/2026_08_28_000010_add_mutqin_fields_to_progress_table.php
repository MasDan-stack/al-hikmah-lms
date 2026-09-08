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
        Schema::table('progress', function (Blueprint $table) {
            $table->boolean('is_mutqin_test')->default(false)->after('nilai_adab');
            $table->unsignedTinyInteger('juz_number')->nullable()->after('is_mutqin_test');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('progress', function (Blueprint $table) {
            $table->dropColumn([
                'is_mutqin_test',
                'juz_number',
            ]);
        });
    }
};
