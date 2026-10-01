<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tạo bảng Orders (Đã bổ sung đầy đủ các cột GHN)
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Người tạo đơn
            $table->string('name');
            $table->string('address');
            $table->string('phone');
            $table->decimal('total_price', 15, 2); // Tổng tiền khách phải thanh toán
            $table->string('status')->default('pending'); // Trạng thái thanh toán
            $table->string('shipping_status')->default('not_shipped'); // Trạng thái GHN

            // Các trường cho GHN
            $table->string('ghn_order_code')->nullable()->index(); // Mã vận đơn GHN
            $table->integer('ghn_total_fee')->default(0); // Phí vận chuyển GHN
            $table->integer('to_district_id')->nullable(); // Mã quận/huyện GHN
            $table->string('to_ward_code')->nullable(); // Mã phường/xã GHN
            $table->timestamps();
        });

        // 2. Tạo bảng OrderItems
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Thuộc order nào
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Thuộc sản phẩm nào
            $table->integer('quantity'); // Số lượng
            $table->decimal('price', 15, 2); // Giá tại thời điểm mua
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Phải xóa bảng con (order_items) trước, bảng cha (orders) sau để không bị lỗi khóa ngoại
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};