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
        Schema::table('mentor_applications', function (Blueprint $table) {
            $table->string('nik', 20)->nullable()->after('full_name')->index();
            $table->string('emergency_contact_name', 150)->nullable()->after('city');
            $table->string('emergency_phone', 25)->nullable()->after('emergency_contact_name');
            $table->string('emergency_relation', 50)->nullable()->after('emergency_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mentor_applications', function (Blueprint $table) {
            $table->dropIndex(['nik']);
            $table->dropColumn(['nik', 'emergency_contact_name', 'emergency_phone', 'emergency_relation']);
        });
    }
};
