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
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignId('enrollment_id')->nullable()->after('program_id')->constrained('enrollments')->nullOnDelete();
            $table->string('payment_purpose', 30)->default('registration')->after('amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['enrollment_id']);
            $table->dropColumn(['enrollment_id', 'payment_purpose']);
        });
    }
};
