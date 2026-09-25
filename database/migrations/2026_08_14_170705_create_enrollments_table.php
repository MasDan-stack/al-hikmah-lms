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
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();

            // Relasi Domain
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained('programs')->cascadeOnDelete();
            $table->decimal('program_price', 12, 2)->nullable(); // Snapshot harga saat daftar
            $table->foreignId('mentor_id')->nullable()->constrained('mentors')->nullOnDelete();

            // Preferensi Awal Orang Tua (Request)
            $table->json('requested_days')->nullable();      // Format: ["monday", "wednesday"]
            $table->time('requested_time')->nullable();      // Format: 15:30:00
            $table->text('parent_notes')->nullable();        // Catatan / preferensi orang tua

            // Penawaran Alternatif Admin (Counter Offer)
            $table->json('offered_days')->nullable();        // Format: ["tuesday", "thursday"]
            $table->time('offered_time')->nullable();        // Format: 16:00:00
            $table->text('admin_notes')->nullable();         // Catatan penjelasan admin

            // Status Pipeline
            $table->string('status', 40)->default('waiting_admin_confirmation');

            // Milestone Timestamps
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->date('start_date')->nullable();

            $table->timestamps();

            // Database Indexing
            $table->index(['status', 'mentor_id']);
            $table->index(['student_id', 'program_id']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
