<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Thêm cột vnp_txn_ref để lưu mã GD VNPAY gửi lên,
     * giúp tra cứu giao dịch khi VNPAY gọi về Return URL / IPN.
     */
    public function up(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('vnp_txn_ref')->nullable()->after('gateway_order_id')->index()
                  ->comment('Mã tham chiếu GD VNPAY (vnp_TxnRef)');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn('vnp_txn_ref');
        });
    }
};
