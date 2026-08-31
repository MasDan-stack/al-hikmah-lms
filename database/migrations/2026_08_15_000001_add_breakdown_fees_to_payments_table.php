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
            if (! Schema::hasColumn('payments', 'registration_fee')) {
                $table->decimal('registration_fee', 12, 2)->default(0)->after('amount');
            }
            if (! Schema::hasColumn('payments', 'program_fee')) {
                $table->decimal('program_fee', 12, 2)->default(0)->after('registration_fee');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (Schema::hasColumn('payments', 'registration_fee')) {
                $table->dropColumn('registration_fee');
            }
            if (Schema::hasColumn('payments', 'program_fee')) {
                $table->dropColumn('program_fee');
            }
        });
    }
};
