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
        Schema::table('galleries', function (Blueprint $table) {
            if (! Schema::hasColumn('galleries', 'slug')) {
                $table->string('slug')->nullable()->after('title')->index();
            }
            if (! Schema::hasColumn('galleries', 'category')) {
                $table->string('category', 50)->default('kegiatan_belajar_mengajar')->after('slug')->index();
            }
            if (! Schema::hasColumn('galleries', 'program_id')) {
                $table->foreignId('program_id')->nullable()->after('category')->constrained('programs')->nullOnDelete();
            }
            if (! Schema::hasColumn('galleries', 'caption')) {
                $table->string('caption')->nullable()->after('image_url');
            }
            if (! Schema::hasColumn('galleries', 'event_date')) {
                $table->date('event_date')->nullable()->after('description')->index();
            }
            if (! Schema::hasColumn('galleries', 'location')) {
                $table->string('location')->nullable()->after('event_date');
            }
            if (! Schema::hasColumn('galleries', 'tags')) {
                $table->json('tags')->nullable()->after('location');
            }
            if (! Schema::hasColumn('galleries', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('tags')->index();
            }
            if (! Schema::hasColumn('galleries', 'is_published')) {
                $table->boolean('is_published')->default(true)->after('is_featured')->index();
            }
            if (! Schema::hasColumn('galleries', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_published')->index();
            }
            if (! Schema::hasColumn('galleries', 'views_count')) {
                $table->unsignedInteger('views_count')->default(0)->after('sort_order');
            }
            if (! Schema::hasColumn('galleries', 'uploaded_by')) {
                $table->foreignId('uploaded_by')->nullable()->after('views_count')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('galleries', 'deleted_at')) {
                $table->softDeletes()->after('updated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('galleries', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropForeign(['program_id']);
            $table->dropForeign(['uploaded_by']);
            $table->dropColumn([
                'slug',
                'category',
                'program_id',
                'caption',
                'event_date',
                'location',
                'tags',
                'is_featured',
                'is_published',
                'sort_order',
                'views_count',
            ]);
        });
    }
};
