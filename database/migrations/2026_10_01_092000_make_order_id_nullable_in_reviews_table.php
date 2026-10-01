<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cho phép order_id và order_item_id nhận null để người dùng có thể đánh giá/bình luận trực tiếp từ trang sản phẩm
        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE reviews MODIFY order_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE reviews MODIFY order_item_id BIGINT UNSIGNED NULL');
        } else {
            Schema::table('reviews', function (Blueprint $table) {
                $table->unsignedBigInteger('order_id')->nullable()->change();
                $table->unsignedBigInteger('order_item_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql' || DB::getDriverName() === 'mariadb') {
            DB::statement('ALTER TABLE reviews MODIFY order_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE reviews MODIFY order_item_id BIGINT UNSIGNED NOT NULL');
        }
    }
};
