<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinanceController extends Controller
{
    /**
     * Hằng số Trạng thái Thanh toán (Payment Status)
     */
    const STATUSES = [
        'pending'   => ['label' => 'Chờ TT', 'color' => 'warning'],
        'paid'      => ['label' => 'Đã TT', 'color' => 'success'],
        'failed'    => ['label' => 'Lỗi TT', 'color' => 'danger'],
        'cancelled' => ['label' => 'Đã hủy', 'color' => 'secondary'],
        'refunded'  => ['label' => 'Đã hoàn tiền', 'color' => 'info'],
    ];

    /**
     * Trạng thái cho phép cập nhật thủ công (đối với COD)
     */
    const COD_TRANSITIONS = [
        'pending'   => ['paid', 'failed', 'cancelled'],
        'paid'      => ['refunded'],
        'failed'    => [],
        'cancelled' => [],
        'refunded'  => [],
    ];

    /**
     * Ưu tiên phương thức thanh toán khi có nhiều giao dịch (Momo > COD)
     */
    const PAYMENT_PRIORITY = ['momo', 'cod'];

    /**
     * Khởi tạo Query chung cho các báo cáo, kết nối Orders và PaymentTransactions
     */
    private function ordersQuery()
    {
        return Order::query()
            ->leftJoin('payment_transactions as pt', function ($join) {
                $join->on('orders.id', '=', 'pt.order_id')
                    // Logic lấy giao dịch ưu tiên nhất nếu 1 đơn có nhiều dòng
                    ->whereRaw('pt.id = (
                        SELECT id FROM payment_transactions pt2 
                        WHERE pt2.order_id = orders.id 
                        ORDER BY FIELD(gateway, "momo", "cod") ASC, created_at DESC 
                        LIMIT 1
                    )');
            })
            ->select(
                'orders.*',
                'pt.id as payment_id',
                'pt.gateway as payment_method',
                'pt.status as payment_status'
            );
    }

    /**
     * Áp dụng bộ lọc từ Request
     */
    private function filteredOrders(Request $request)
    {
        $query = $this->ordersQuery();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('orders.id', 'like', "%{$search}%")
                  ->orWhere('orders.name', 'like', "%{$search}%")
                  ->orWhere('orders.phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('orders.created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('orders.created_at', '<=', $request->date_to);
        }

        if ($request->filled('amount_min')) {
            $query->where('orders.total_price', '>=', $request->amount_min);
        }

        if ($request->filled('amount_max')) {
            $query->where('orders.total_price', '<=', $request->amount_max);
        }

        if ($request->filled('method') && $request->method !== 'all') {
            if ($request->method === 'unknown') {
                $query->whereNull('pt.gateway');
            } else {
                $query->where('pt.gateway', $request->method);
            }
        }

        if ($request->filled('payment_status') && $request->payment_status !== 'all') {
            $query->where('pt.status', $request->payment_status);
        }

        return $query;
    }

    /**
     * Trang Index - Thống kê chỉ số
     */
    public function index(Request $request)
    {
        $query = $this->filteredOrders($request);

        // Lấy tất cả kết quả để thống kê
        $allOrders = $query->get();

        // 1. Tổng quan
        $statusTotals = [
            'total_amount' => $allOrders->sum('total_price'),
            'pending'      => $allOrders->where('payment_status', 'pending')->sum('total_price'),
            'paid'         => $allOrders->where('payment_status', 'paid')->sum('total_price'),
            'failed'       => $allOrders->where('payment_status', 'failed')->sum('total_price'),
            'cancelled'    => $allOrders->where('payment_status', 'cancelled')->sum('total_price'),
            'refunded'     => $allOrders->where('payment_status', 'refunded')->sum('total_price'),
        ];

        // Tách riêng chờ MoMo và COD
        $statusTotals['momo_pending'] = $allOrders->where('payment_status', 'pending')
                                                  ->where('payment_method', 'momo')
                                                  ->sum('total_price');
        $statusTotals['cod_pending']  = $allOrders->where('payment_status', 'pending')
                                                  ->where('payment_method', 'cod')
                                                  ->sum('total_price');

        // 2. Thống kê theo phương thức
        $methods = ['cod', 'momo'];
        $methodTotals = [];
        foreach ($methods as $m) {
            $mOrders = $allOrders->where('payment_method', $m);
            $methodTotals[$m] = [
                'count'      => $mOrders->count(),
                'total'      => $mOrders->sum('total_price'),
                'paid_total' => $mOrders->where('payment_status', 'paid')->sum('total_price'),
            ];
        }

        return view('admin.finance.index', compact('statusTotals', 'methodTotals'));
    }

    /**
     * Trang Danh sách giao dịch
     */
    public function transactions(Request $request)
    {
        $query = $this->filteredOrders($request);

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('orders.created_at', 'asc');
                break;
            case 'price_asc':
                $query->orderBy('orders.total_price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('orders.total_price', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('orders.created_at', 'desc');
                break;
        }

        $orders = $query->paginate(20)->withQueryString();
        
        $statuses = self::STATUSES;
        $codTransitions = self::COD_TRANSITIONS;

        return view('admin.finance.transactions', compact('orders', 'statuses', 'codTransitions'));
    }

    /**
     * Cập nhật trạng thái thủ công (Dành cho COD)
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'payment_id' => 'required|exists:payment_transactions,id',
            'status'     => 'required|string',
        ]);

        $transaction = PaymentTransaction::where('id', $request->payment_id)
            ->where('order_id', $order->id)
            ->firstOrFail();

        // Chỉ cho phép cập nhật COD hoặc các TH đặc biệt
        if ($transaction->gateway !== 'cod' && auth()->user()->role !== 'super_admin') {
            return back()->with('error', 'Chỉ có thể cập nhật trạng thái thủ công cho đơn COD.');
        }

        $currentStatus = $transaction->status;
        $newStatus = $request->status;

        if (!array_key_exists($newStatus, self::STATUSES)) {
            return back()->with('error', 'Trạng thái không hợp lệ.');
        }

        // Cập nhật
        $transaction->update(['status' => $newStatus]);

        // Đồng bộ trạng thái đơn hàng nếu cần (tùy logic nghiệp vụ)
        if ($newStatus === 'paid') {
            $order->update(['status' => 'paid']);
        } elseif ($newStatus === 'cancelled') {
            $order->update(['status' => 'cancelled']);
        }

        return back()->with('success', "Đã cập nhật trạng thái thanh toán thành công.");
    }
}
