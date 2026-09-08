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
        Schema::create('mentor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->onDelete('cascade');
            $table->enum('day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('max_students')->default(5);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_holiday')->default(false);
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->unique(['mentor_id', 'day']);
            $table->index(['mentor_id', 'day', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mentor_availabilities');
    }
};
