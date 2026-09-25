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
        Schema::table('programs', function (Blueprint $table) {
            $table->string('category')->default('anak')->after('name'); // 'anak', 'dewasa', 'bahasa_arab'
            $table->string('icon')->default('bi-book-half')->after('category');
            $table->boolean('is_popular')->default(false)->after('level');
            $table->boolean('is_active')->default(true)->after('is_popular');
            $table->integer('sort_order')->default(0)->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['category', 'icon', 'is_popular', 'is_active', 'sort_order']);
        });
    }
};
