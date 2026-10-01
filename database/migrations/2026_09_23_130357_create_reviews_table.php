<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Bảng đánh giá sản phẩm.
     * Ràng buộc: 1 user chỉ được đánh giá 1 sản phẩm trong 1 đơn hàng.
     */
    public function up(): void
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained('order_items')->cascadeOnDelete();
            $table->tinyInteger('rating')->unsigned()->comment('1–5 sao');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Mỗi order_item chỉ được đánh giá 1 lần
            $table->unique('order_item_id', 'unique_review_per_order_item');
            $table->index(['product_id', 'rating']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
