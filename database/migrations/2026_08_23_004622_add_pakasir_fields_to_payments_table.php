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
            $table->string('checkout_url')->nullable()->after('payment_method');
            $table->text('qr_content')->nullable()->after('checkout_url');
            $table->string('pakasir_order_id')->nullable()->index()->after('qr_content');
            $table->decimal('admin_fee', 12, 2)->default(0)->after('pakasir_order_id');
            $table->decimal('total_amount', 12, 2)->nullable()->after('admin_fee');
            $table->timestamp('expired_at')->nullable()->after('total_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['checkout_url', 'qr_content', 'pakasir_order_id', 'admin_fee', 'total_amount', 'expired_at']);
        });
    }
};
