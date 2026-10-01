<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard(): View
    {
        // 1. Tổng doanh thu (Chỉ tính những đơn đã thanh toán: MoMo paid, hoặc COD đã paid)
        $totalRevenue = Order::whereIn('status', ['paid', 'cod_paid'])->sum('total_price');

        // 2. Tổng đơn hàng
        $totalOrders = Order::count();

        // Đếm theo trạng thái
        // - Chờ xử lý: pending, cod_ordered
        $pendingOrders = Order::whereIn('status', ['pending', 'cod_ordered'])
            ->whereNotIn('shipping_status', ['cancelled', 'delivered'])
            ->count();

        // - Đang giao: các trạng thái shipping_status tương ứng
        $deliveringStatuses = ['picking', 'picked', 'storing', 'transporting', 'sorting', 'delivering'];
        $deliveringOrders = Order::whereIn('shipping_status', $deliveringStatuses)->count();

        // - Đã hoàn thành (giao thành công)
        $completedOrders = Order::where('shipping_status', 'delivered')->count();

        // - Đã hủy
        $cancelledOrders = Order::where('status', 'cancelled')->orWhere('shipping_status', 'cancelled')->count();

        // 3. Tổng số khách hàng
        $totalCustomers = User::where('role', 'user')->count();

        // 4. Doanh thu theo 30 ngày gần nhất
        $thirtyDaysAgo = Carbon::now()->subDays(29)->startOfDay();
        
        $dailyRevenues = Order::whereIn('status', ['paid', 'cod_paid'])
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_price) as revenue')
            )
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->keyBy('date');

        $chartLabels = [];
        $chartData = [];

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = Carbon::parse($date)->format('d/m');
            $chartData[] = $dailyRevenues->has($date) ? $dailyRevenues->get($date)->revenue : 0;
        }

        // 5. Top 5 sản phẩm bán chạy nhất
        $topProducts = Product::select('products.id', 'products.name', 'products.image', 'products.price')
            ->selectRaw('SUM(order_items.quantity) as total_sold')
            ->selectRaw('SUM(order_items.quantity * order_items.price) as total_revenue')
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereIn('orders.status', ['paid', 'cod_paid'])
            ->groupBy('products.id', 'products.name', 'products.image', 'products.price')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'totalOrders',
            'pendingOrders',
            'deliveringOrders',
            'completedOrders',
            'cancelledOrders',
            'totalCustomers',
            'chartLabels',
            'chartData',
            'topProducts'
        ));
    }
}