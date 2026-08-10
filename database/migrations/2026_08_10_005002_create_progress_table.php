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
        Schema::create('progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->nullable()->constrained('learning_sessions')->nullOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->string('kategori')->default('Tahfidz');
            $table->string('surah_start')->nullable();
            $table->string('surah_end')->nullable();
            $table->integer('ayat_start')->nullable();
            $table->integer('ayat_end')->nullable();
            $table->integer('juz')->nullable();
            $table->integer('nilai_fluent')->default(80);
            $table->integer('nilai_tajwid')->default(80);
            $table->integer('nilai_adab')->default(80);
            $table->text('catatan_evaluasi')->nullable();
            $table->text('homework')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progress');
    }
};
