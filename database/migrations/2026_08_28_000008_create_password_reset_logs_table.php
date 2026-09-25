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
        Schema::create('password_reset_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('reset_method', ['self', 'parent', 'admin'])->default('self');
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->enum('notification_channel', ['whatsapp', 'email', 'inapp'])->default('whatsapp');
            $table->enum('notification_status', ['sent', 'failed', 'fallback'])->default('sent');
            $table->timestamps();

            $table->index('user_id', 'idx_user');
            $table->index('created_at', 'idx_created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('password_reset_logs');
    }
};
