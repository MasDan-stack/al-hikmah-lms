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
        Schema::create('trial_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('parent_name');
            $table->string('child_name');
            $table->string('whatsapp', 30);
            $table->string('child_age', 50)->nullable();
            $table->enum('gender', ['L', 'P'])->default('L');
            $table->foreignId('program_id')->nullable()->constrained('programs')->nullOnDelete();
            $table->string('trial_focus')->default('iqra_placement'); // iqra_placement, tahsin_tajwid, tahfidz_hafalan, bahasa_arab
            $table->date('preferred_date')->nullable();
            $table->string('preferred_time_slot', 100)->nullable(); // pagi, siang, sore, malam
            $table->enum('learning_method', ['online', 'offline'])->default('online');
            $table->string('city')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'contacted', 'scheduled', 'completed', 'cancelled'])->default('pending');
            $table->foreignId('assigned_mentor_id')->nullable()->constrained('mentors')->nullOnDelete();
            $table->dateTime('scheduled_at')->nullable();
            $table->text('assessment_result')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('whatsapp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trial_bookings');
    }
};
