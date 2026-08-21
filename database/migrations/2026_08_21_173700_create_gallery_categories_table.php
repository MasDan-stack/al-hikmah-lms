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
        if (! Schema::hasTable('gallery_categories')) {
            Schema::create('gallery_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('group', 50)->default('Kategori Utama');
                $table->string('icon', 50)->default('bi-images');
                $table->string('badge_class', 100)->default('bg-success');
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->integer('sort_order')->default(0)->index();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        Schema::table('galleries', function (Blueprint $table) {
            if (! Schema::hasColumn('galleries', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('category')->constrained('gallery_categories')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            if (Schema::hasColumn('galleries', 'category_id')) {
                $table->dropForeign(['category_id']);
                $table->dropColumn('category_id');
            }
        });

        Schema::dropIfExists('gallery_categories');
    }
};
