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
        Schema::table('session_confirmations', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'izin', 'sakit', 'terlambat'])->default('hadir')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_confirmations', function (Blueprint $table) {
            $table->enum('status', ['hadir', 'izin', 'sakit'])->default('hadir')->change();
        });
    }
};
