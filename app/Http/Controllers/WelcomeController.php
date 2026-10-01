<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class WelcomeController extends Controller
{
    public function index(): View
    {
        // Sản phẩm mới nhất để hiển thị trong lưới sản phẩm nổi bật
        $products = Product::with('category')
            ->where('stock_quantity', '>', 0)
            ->latest()
            ->take(8)
            ->get();

        // Top 5 sản phẩm bán chạy nhất — tính theo tổng số lượng trong order_items
        // Chỉ lấy đơn hàng không bị hủy (status != cancelled)
        $bestSellers = Product::with('category')
            ->select('products.*')
            ->selectSub(function ($query) {
                $query->from('order_items')
                    ->selectRaw('COALESCE(SUM(order_items.quantity), 0)')
                    ->join('orders', 'orders.id', '=', 'order_items.order_id')
                    ->whereColumn('order_items.product_id', 'products.id')
                    ->where('orders.status', '!=', 'cancelled');
            }, 'total_sold')
            ->where('stock_quantity', '>', 0)
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        $categories = Category::withCount(['products' => function ($q) {
            $q->where('stock_quantity', '>', 0);
        }])->get();

        return view('welcome', compact('products', 'bestSellers', 'categories'));
    }
}