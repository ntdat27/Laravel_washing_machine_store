<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\GHNService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    // ==========================================
    // ĐƠN HÀNG CỦA TÔI
    // ==========================================

    /**
     * Hiển thị lịch sử đơn hàng của user đang đăng nhập.
     */
    public function myOrders()
    {
        $orders = Order::with(['items.product', 'items.variant', 'items.review'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('user.orders.index', compact('orders'));
    }

    /**
     * User hủy đơn hàng của mình.
     * Chỉ cho phép hủy khi shipping_status ∈ {pending, not_shipped, processing}.
     * Hoàn lại tồn kho sau khi hủy.
     */
    public function cancel(Order $order)
    {
        // Bảo vệ: chỉ chủ đơn mới được hủy
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Bạn không có quyền hủy đơn hàng này.');
        }

        // Chỉ cho phép hủy khi chưa vào giai đoạn lấy hàng
        $cancellableStatuses = ['pending', 'not_shipped', 'processing'];
        if (!in_array($order->shipping_status, $cancellableStatuses)) {
            return redirect()->route('user.orders.index')
                ->with('error', 'Đơn hàng đang trong quá trình vận chuyển, không thể hủy. Vui lòng liên hệ hỗ trợ.');
        }

        DB::transaction(function () use ($order) {
            // Hoàn lại tồn kho
            $order->load('items');
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)
                    ->increment('stock_quantity', $item->quantity);

                if ($item->variant_id) {
                    ProductVariant::where('id', $item->variant_id)
                        ->increment('stock', $item->quantity);
                }
            }

            // Cập nhật trạng thái sang đã hủy
            $order->update([
                'status'          => 'cancelled',
                'shipping_status' => 'cancelled',
            ]);
        });

        Log::info('User #' . Auth::id() . " đã hủy đơn hàng #{$order->id}");

        return redirect()->route('user.orders.index')
            ->with('success', 'Đơn hàng #DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT) . ' đã được hủy. Tồn kho đã được hoàn lại.');
    }

    // ==========================================
    // API GHN — TRA CỨU ĐỊA CHỈ & PHÍ SHIP
    // ==========================================

    public function getProvinces(GHNService $ghn)
    {
        return response()->json($ghn->getProvinces());
    }

    public function getDistricts(int $provinceId, GHNService $ghn)
    {
        return response()->json($ghn->getDistricts($provinceId));
    }

    public function getWards(int $districtId, GHNService $ghn)
    {
        return response()->json($ghn->getWards($districtId));
    }

    public function getShippingFee(Request $request, GHNService $ghn)
    {
        $cart = session('cart', []);
        $totalWeight = 0;

        foreach ($cart as $item) {
            $totalWeight += ((int) ($item['weight'] ?? 200)) * (int) $item['quantity'];
        }

        $res = $ghn->calculateFee([
            'service_type_id'  => 2,
            'from_district_id' => (int) config('services.ghn.from_district_id'),
            'to_district_id'   => (int) $request->to_district_id,
            'to_ward_code'     => (string) $request->to_ward_code,
            'weight'           => $totalWeight > 0 ? $totalWeight : 300,
            'length'           => 15,
            'width'            => 15,
            'height'           => 10,
        ]);

        return response()->json($res);
    }
}