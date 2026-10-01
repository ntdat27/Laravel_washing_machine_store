<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    private const TABS = [
        'all' => ['label' => 'Tất cả', 'color' => 'primary', 'statuses' => []],
        'pending' => ['label' => 'Chờ xử lý', 'color' => 'secondary', 'statuses' => ['pending', 'not_shipped', 'processing']],
        'ready' => ['label' => 'Chờ lấy hàng', 'color' => 'info', 'statuses' => ['ready_to_pick']],
        'picking' => ['label' => 'Đang lấy hàng', 'color' => 'info', 'statuses' => ['picking']],
        'delivering' => ['label' => 'Đang giao', 'color' => 'warning', 'statuses' => ['delivering', 'picked', 'storing', 'transporting', 'sorting']],
        'delivered' => ['label' => 'Thành công', 'color' => 'success', 'statuses' => ['delivered']],
        'return' => ['label' => 'Hoàn hàng', 'color' => 'orange', 'statuses' => ['return', 'returning', 'returned', 'return_transporting', 'return_sorting']],
        'cancelled' => ['label' => 'Đã hủy', 'color' => 'danger', 'statuses' => ['cancelled']],
    ];

    public function index(Request $request)
    {
        $paymentLabels = [
            'pending' => 'Chờ thanh toán',
            'initiated' => 'Đang chờ MoMo',
            'paid' => 'Đã thanh toán',
            'failed' => 'Thanh toán thất bại',
            'cancelled' => 'Đã hủy',
            'refund_pending' => 'Chờ hoàn tiền',
            'refunded' => 'Đã hoàn tiền',
        ];

        $shippingLabels = [
            'pending' => 'Chờ tạo vận đơn',
            'not_shipped' => 'Chưa giao hàng',
            'processing' => 'Đang tạo vận đơn',
            'ready_to_pick' => 'Chờ lấy hàng',
            'picking' => 'Đang lấy hàng',
            'picked' => 'Đã lấy hàng',
            'storing' => 'Đang lưu kho',
            'transporting' => 'Đang trung chuyển',
            'sorting' => 'Đang phân loại',
            'delivering' => 'Đang giao hàng',
            'delivered' => 'Giao hàng thành công',
            'return' => 'Chờ hoàn hàng',
            'returning' => 'Đang hoàn hàng',
            'returned' => 'Đã hoàn hàng',
            'return_transporting' => 'Đang chuyển hoàn',
            'return_sorting' => 'Đang phân loại hoàn',
            'cancelled' => 'Đã hủy',
        ];

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(['pending', 'paid', 'paid_momo', 'cod_ordered', 'cod_paid', 'cancelled'])],
            'payment_status' => ['nullable', Rule::in(array_keys($paymentLabels))],
            'shipping_status' => ['nullable', Rule::in(array_keys($shippingLabels))],
            'gateway' => ['nullable', Rule::in(['cod', 'momo', 'unknown'])],
            'tab' => ['nullable', Rule::in(array_keys(self::TABS))],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('date_from') ? ['after_or_equal:date_from'] : [])],
            'per_page' => ['nullable', 'integer', Rule::in([25, 50, 100])],
            'sort' => ['nullable', Rule::in(['newest', 'oldest', 'amount_desc', 'amount_asc'])],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $paymentId = DB::table('payment_transactions')->select('id')->whereColumn('order_id', 'orders.id')
            ->orderByRaw("CASE WHEN status IN ('paid', 'refund_pending', 'refunded') THEN 0 ELSE 1 END")
            ->orderByDesc('id')->limit(1);

        $source = DB::table('orders')->leftJoin('payment_transactions as payment', function ($join) use ($paymentId) {
            $join->on('payment.order_id', '=', 'orders.id')->where('payment.id', '=', $paymentId);
        })->select('orders.*')
            ->selectRaw("COALESCE(payment.gateway, CASE WHEN orders.status IN ('cod_ordered', 'cod_paid') THEN 'cod' WHEN orders.status IN ('paid', 'paid_momo') THEN 'momo' ELSE 'unknown' END) as gateway")
            ->selectRaw("COALESCE(payment.status, CASE WHEN orders.status = 'cod_ordered' THEN 'pending' WHEN orders.status IN ('cod_paid', 'paid_momo') THEN 'paid' ELSE orders.status END) as payment_status");

        $query = Order::query()->fromSub($source, 'orders');

        foreach (['status', 'payment_status', 'gateway'] as $field) {
            if ($request->filled($field)) {
                $query->where($field, $filters[$field]);
            }
        }

        if ($request->filled('search')) {
            $search = trim($filters['search']);
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('ghn_order_code', 'like', '%' . $search . '%')
                    ->orWhereHas('items.product', fn($products) => $products->where('name', 'like', '%' . $search . '%'));

                if (preg_match('/^(?:\#|DH)?0*(\d+)$/i', $search, $matches)) {
                    $query->orWhere('orders.id', $matches[1]);
                }
            });
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($filters['date_from'])->startOfDay());
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<', Carbon::parse($filters['date_to'])->addDay()->startOfDay());
        }

        $shippingCounts = (clone $query)->select('shipping_status')->selectRaw('COUNT(*) as total')
            ->groupBy('shipping_status')->pluck('total', 'shipping_status');

        $tabs = collect(self::TABS)->map(function ($tab, $key) use ($shippingCounts) {
            $tab['count'] = $key === 'all' ? $shippingCounts->sum() : collect($tab['statuses'])->sum(fn($status) => $shippingCounts->get($status, 0));
            return $tab;
        });

        $activeTab = $filters['tab'] ?? 'all';
        if ($activeTab !== 'all') {
            $query->whereIn('shipping_status', self::TABS[$activeTab]['statuses']);
        }

        if ($request->filled('shipping_status')) {
            $query->where('shipping_status', $filters['shipping_status']);
        }

        [$column, $direction] = match ($filters['sort'] ?? 'newest') {
            'oldest' => ['created_at', 'asc'],
            'amount_desc' => ['total_price', 'desc'],
            'amount_asc' => ['total_price', 'asc'],
            default => ['created_at', 'desc'],
        };

        $orders = $query->with('items.product')->orderBy($column, $direction)->orderBy('id', $direction)
            ->paginate((int) ($filters['per_page'] ?? 25))->withQueryString();

        return view('admin.orders.index', compact('orders', 'filters', 'tabs', 'activeTab', 'paymentLabels', 'shippingLabels'));
    }

    public function show($id)
    {
        $order = Order::with([
            'user',
            'items.product',
            'items.variant',
            'paymentTransactions' => function ($query) {
                $query->latest();
            }
        ])->findOrFail($id);

        // Định nghĩa các chuyển đổi trạng thái hợp lệ để truyền sang view
        $allowedTransitions = $this->getAllowedTransitions($order->shipping_status);

        return view('admin.orders.show', compact('order', 'allowedTransitions'));
    }

    /**
     * Admin cập nhật trạng thái đơn hàng.
     * Khi chuyển sang 'cancelled' → tự động hoàn lại tồn kho.
     */
    public function update(Request $request, $id)
    {
        $order = Order::with('items.variant')->findOrFail($id);

        $validated = $request->validate([
            'shipping_status' => ['required', 'string', Rule::in([
                'pending', 'not_shipped', 'processing',
                'ready_to_pick', 'picking', 'picked',
                'storing', 'transporting', 'sorting',
                'delivering', 'delivered',
                'return', 'returning', 'returned',
                'return_transporting', 'return_sorting',
                'cancelled',
            ])],
        ]);

        $newStatus   = $validated['shipping_status'];
        $oldStatus   = $order->shipping_status;
        $isCancelling = $newStatus === 'cancelled' && $oldStatus !== 'cancelled';

        // Không cho phép sửa đơn đã hoàn thành cuối cùng
        $finalStatuses = ['delivered', 'returned', 'cancelled'];
        if (in_array($oldStatus, $finalStatuses)) {
            return redirect()->route('admin.orders.show', $id)
                ->with('error', "Không thể thay đổi trạng thái đơn hàng đã ở trạng thái cuối: {$oldStatus}.");
        }

        DB::transaction(function () use ($order, $newStatus, $isCancelling) {
            $updateData = ['shipping_status' => $newStatus];

            // Nếu hủy đơn → cập nhật thêm status thanh toán
            if ($isCancelling) {
                $updateData['status'] = 'cancelled';
                $this->restoreStock($order);
            }

            // Nếu giao thành công → đánh dấu COD đã thanh toán
            if ($newStatus === 'delivered' && in_array($order->status, ['cod_ordered', 'pending'])) {
                $updateData['status'] = 'cod_paid';
            }

            $order->update($updateData);
        });

        $label = $isCancelling ? 'Đã hủy và hoàn lại tồn kho' : 'Cập nhật trạng thái';
        return redirect()->route('admin.orders.show', $id)
            ->with('success', "{$label} thành công! Trạng thái mới: {$newStatus}.");
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /**
     * Trả về danh sách trạng thái tiếp theo hợp lệ từ trạng thái hiện tại.
     */
    private function getAllowedTransitions(string $currentStatus): array
    {
        $map = [
            'pending'              => ['not_shipped', 'processing', 'cancelled'],
            'not_shipped'          => ['processing', 'ready_to_pick', 'cancelled'],
            'processing'           => ['ready_to_pick', 'cancelled'],
            'ready_to_pick'        => ['picking', 'cancelled'],
            'picking'              => ['picked', 'cancelled'],
            'picked'               => ['storing', 'transporting', 'delivering'],
            'storing'              => ['transporting', 'delivering'],
            'transporting'         => ['sorting', 'delivering'],
            'sorting'              => ['delivering'],
            'delivering'           => ['delivered', 'return'],
            'delivered'            => [], // Trạng thái cuối
            'return'               => ['returning'],
            'returning'            => ['return_transporting', 'return_sorting', 'returned'],
            'return_transporting'  => ['return_sorting', 'returned'],
            'return_sorting'       => ['returned'],
            'returned'             => [], // Trạng thái cuối
            'cancelled'            => [], // Trạng thái cuối
        ];

        return $map[$currentStatus] ?? [];
    }

    /**
     * Hoàn lại tồn kho khi hủy đơn hàng.
     * Trả lại cả stock variant và stock_quantity tổng của sản phẩm.
     */
    private function restoreStock(Order $order): void
    {
        $order->load('items');
        foreach ($order->items as $item) {
            // Hoàn lại tồn kho tổng
            Product::where('id', $item->product_id)
                ->increment('stock_quantity', $item->quantity);

            // Hoàn lại tồn kho variant nếu có
            if ($item->variant_id) {
                ProductVariant::where('id', $item->variant_id)
                    ->increment('stock', $item->quantity);
            }
        }
    }
}