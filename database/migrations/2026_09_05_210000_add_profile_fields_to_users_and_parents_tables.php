<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            if (! Schema::hasColumn('parents', 'maps_link')) {
                $table->string('maps_link', 500)->nullable()->after('address');
            }
        });

        Schema::table('mentors', function (Blueprint $table) {
            if (! Schema::hasColumn('mentors', 'address')) {
                $table->text('address')->nullable()->after('bio');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (! Schema::hasColumn('students', 'nickname')) {
                $table->string('nickname', 50)->nullable()->after('full_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('parents', function (Blueprint $table) {
            if (Schema::hasColumn('parents', 'maps_link')) {
                $table->dropColumn('maps_link');
            }
        });

        Schema::table('mentors', function (Blueprint $table) {
            if (Schema::hasColumn('mentors', 'address')) {
                $table->dropColumn('address');
            }
        });

        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'nickname')) {
                $table->dropColumn('nickname');
            }
        });
    }
};
