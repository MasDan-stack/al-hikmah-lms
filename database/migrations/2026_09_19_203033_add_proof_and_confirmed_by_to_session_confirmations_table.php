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
            $table->string('proof_image')->nullable()->after('notes');
            $table->string('confirmed_by', 30)->default('parent')->after('proof_image');
            $table->timestamp('verified_at')->nullable()->after('confirmed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('session_confirmations', function (Blueprint $table) {
            $table->dropColumn(['proof_image', 'confirmed_by', 'verified_at']);
        });
    }
};
