<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'brand',
        'capacity_kg',
        'price',
        'stock_quantity',
        'image',
        'description'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /** Điểm đánh giá trung bình (0 nếu chưa có review) */
    public function avgRating(): float
    {
        return round((float) ($this->reviews()->avg('rating') ?? 0), 1);
    }
    /**
     * Tính tổng số lượng sản phẩm đã bán (chỉ tính các đơn hàng đã thanh toán)
     */
    public function totalSold()
    {
        return \App\Models\OrderItem::where('product_id', $this->id)
            ->whereHas('order', function ($query) {
                $query->whereIn('status', ['paid', 'cod_paid']);
            })->sum('quantity');
    }
}