<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ReviewController extends Controller
{
    /**
     * Lưu đánh giá mới.
     *
     * Điều kiện:
     * 1. Đơn hàng phải của user đang đăng nhập.
     * 2. Đơn hàng phải ở trạng thái 'delivered'.
     * 3. Sản phẩm (order_item) phải thuộc đơn hàng đó.
     * 4. Chưa từng đánh giá order_item này.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'      => 'required|integer|exists:orders,id',
            'order_item_id' => 'required|integer|exists:order_items,id',
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'nullable|string|max:1000',
        ]);

        $order = Order::find($validated['order_id']);

        // Bảo vệ: chỉ chủ đơn mới được đánh giá
        if ($order->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền đánh giá đơn hàng này.'], 403);
        }

        // Điều kiện: đơn phải đã giao
        if ($order->shipping_status !== 'delivered') {
            return response()->json(['success' => false, 'message' => 'Chỉ có thể đánh giá sau khi đơn hàng được giao thành công.'], 422);
        }

        // Lấy order_item và kiểm tra thuộc đúng đơn
        $orderItem = OrderItem::find($validated['order_item_id']);
        if (!$orderItem || $orderItem->order_id !== $order->id) {
            return response()->json(['success' => false, 'message' => 'Sản phẩm không thuộc đơn hàng này.'], 422);
        }

        // Kiểm tra đã đánh giá chưa
        $exists = Review::where('order_item_id', $orderItem->id)->exists();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi.'], 422);
        }

        $review = Review::create([
            'user_id'       => Auth::id(),
            'product_id'    => $orderItem->product_id,
            'order_id'      => $order->id,
            'order_item_id' => $orderItem->id,
            'rating'        => $validated['rating'],
            'comment'       => $validated['comment'],
        ]);

        return response()->json([
            'success'    => true,
            'message'    => 'Cảm ơn bạn đã đánh giá sản phẩm!',
            'review_id'  => $review->id,
        ]);
    }
}
