<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->onDelete('cascade');
            $table->date('leave_date');
            $table->string('reason')->nullable();
            $table->foreignId('substitute_mentor_id')->nullable()->constrained('mentors')->nullOnDelete();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->timestamps();

            $table->unique(['mentor_id', 'leave_date']);
            $table->index(['leave_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_leaves');
    }
};
