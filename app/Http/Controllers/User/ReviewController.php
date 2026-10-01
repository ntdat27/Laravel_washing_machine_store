<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Lưu nhận xét và đánh giá sản phẩm.
     * Hỗ trợ 2 phương thức:
     * 1. Gửi trực tiếp từ trang chi tiết sản phẩm (product_id, rating, comment).
     * 2. Gửi từ lịch sử đơn hàng (order_id, order_item_id, rating, comment).
     */
    public function store(Request $request)
    {
        // -------------------------------------------------------------
        // TRƯỜNG HỢP 1: Đánh giá qua đơn hàng đã mua (từ trang Lịch sử đơn hàng)
        // -------------------------------------------------------------
        if ($request->filled('order_id') && $request->filled('order_item_id')) {
            $validated = $request->validate([
                'order_id'      => 'required|integer|exists:orders,id',
                'order_item_id' => 'required|integer|exists:order_items,id',
                'rating'        => 'required|integer|min:1|max:5',
                'comment'       => 'nullable|string|max:1000',
            ]);

            $order = Order::find($validated['order_id']);

            if (!$order || $order->user_id !== Auth::id()) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Bạn không có quyền đánh giá đơn hàng này.'], 403);
                }
                return back()->with('error', 'Bạn không có quyền đánh giá đơn hàng này.');
            }

            if ($order->shipping_status !== 'delivered') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Chỉ có thể đánh giá sau khi đơn hàng được giao thành công.'], 422);
                }
                return back()->with('error', 'Chỉ có thể đánh giá sau khi đơn hàng được giao thành công.');
            }

            $orderItem = OrderItem::find($validated['order_item_id']);
            if (!$orderItem || $orderItem->order_id !== $order->id) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Sản phẩm không thuộc đơn hàng này.'], 422);
                }
                return back()->with('error', 'Sản phẩm không thuộc đơn hàng này.');
            }

            $exists = Review::where('order_item_id', $orderItem->id)->exists();
            if ($exists) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này rồi.'], 422);
                }
                return back()->with('error', 'Bạn đã đánh giá sản phẩm này rồi.');
            }

            $review = Review::create([
                'user_id'       => Auth::id(),
                'product_id'    => $orderItem->product_id,
                'order_id'      => $order->id,
                'order_item_id' => $orderItem->id,
                'rating'        => $validated['rating'],
                'comment'       => $validated['comment'] ?? null,
            ]);

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success'   => true,
                    'message'   => 'Cảm ơn bạn đã đánh giá sản phẩm!',
                    'review_id' => $review->id,
                ]);
            }

            return back()->with('success', 'Cảm ơn bạn đã gửi đánh giá sản phẩm!');
        }

        // -------------------------------------------------------------
        // TRƯỜNG HỢP 2: Đánh giá/Bình luận trực tiếp từ Trang chi tiết sản phẩm
        // -------------------------------------------------------------
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'comment'    => 'required|string|max:1000',
        ]);

        $productId = (int) $validated['product_id'];

        // Kiểm tra xem khách hàng này đã từng mua sản phẩm để liên kết đơn nếu có
        $orderItem = OrderItem::where('product_id', $productId)
            ->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->latest()
            ->first();

        // Kiểm tra xem khách đã gửi nhận xét cho sản phẩm này chưa
        $existingReview = Review::where('user_id', Auth::id())
            ->where('product_id', $productId)
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating'  => $validated['rating'],
                'comment' => $validated['comment'],
            ]);
            $msg = 'Cảm ơn bạn! Đánh giá của bạn đã được cập nhật thành công.';
        } else {
            Review::create([
                'user_id'       => Auth::id(),
                'product_id'    => $productId,
                'order_id'      => $orderItem?->order_id,
                'order_item_id' => $orderItem?->id,
                'rating'        => $validated['rating'],
                'comment'       => $validated['comment'],
            ]);
            $msg = 'Cảm ơn bạn đã gửi nhận xét và đánh giá sản phẩm!';
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return back()->with('success', $msg);
    }
}
