<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mentor_calendar_syncs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mentor_id')->constrained('mentors')->cascadeOnDelete();
            $table->string('provider', 30); // 'google', 'outlook', 'ical'
            $table->text('access_token')->nullable(); // Terenkripsi otomatis via cast
            $table->text('refresh_token')->nullable();
            $table->dateTime('token_expires_at')->nullable();
            $table->string('calendar_id')->nullable()->default('primary');
            $table->string('sync_token')->nullable();
            $table->dateTime('last_synced_at')->nullable();
            $table->json('busy_slots_cache')->nullable(); // Cache jam sibuk pekan berjalan
            $table->string('ical_token', 64)->unique()->nullable();
            $table->boolean('privacy_mode')->default(true); // Hanya baca status sibuk tanpa judul
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['mentor_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mentor_calendar_syncs');
    }
};
