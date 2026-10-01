<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\PaymentTransaction;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\MomoService;
use App\Services\GHNOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * MomoController — Xử lý toàn bộ luồng thanh toán MoMo:
 *
 *   GET  /orders/{order}/start-momo   → Khởi động TT cho đơn vừa tạo
 *   GET  /orders/{order}/pay/momo     → Thanh toán lại đơn cũ (pending)
 *   GET  /payment/momo/callback       → Return URL (user được redirect về)
 *   POST /payment/momo/ipn            → IPN server-to-server từ MoMo
 */
class MomoController extends Controller
{
    // ==========================================
    // KHỞI ĐỘNG THANH TOÁN
    // ==========================================

    /**
     * Được gọi ngay sau khi CartController tạo đơn hàng MoMo.
     * Tạo transaction mới và redirect sang MoMo.
     */
    public function start(Order $order, MomoService $momo)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        // Nếu đơn đã paid → redirect về đơn hàng
        if (in_array($order->status, ['paid', 'cod_ordered', 'cod_paid'])) {
            return redirect()->route('user.orders.index')
                ->with('info', 'Đơn hàng này đã được thanh toán.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    /**
     * Thanh toán lại đơn hàng MoMo cũ chưa trả tiền.
     */
    public function payAgain(Order $order, MomoService $momo)
    {
        if ($order->user_id !== Auth::id()) abort(403);

        if ($order->status === 'paid') {
            return redirect()->route('user.orders.index')
                ->with('info', 'Đơn hàng này đã được thanh toán rồi.');
        }

        return $this->redirectToMomo($order, $this->newTransaction($order), $momo);
    }

    // ==========================================
    // RETURN URL — User được redirect về sau TT
    // ==========================================

    /**
     * MoMo redirect user về đây sau khi thanh toán (thành công hoặc thất bại).
     * Hiển thị kết quả cho user — DB update được thực hiện chắc chắn qua IPN.
     * Nhưng để an toàn, gọi completePayment nếu IPN chưa kịp cập nhật.
     */
    public function callback(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        $payload = $request->all();
        Log::info('[MoMo Callback] Nhận redirect', ['resultCode' => $payload['resultCode'] ?? 'N/A']);

        // Chữ ký không hợp lệ
        if (!$momo->isValidResponse($payload)) {
            Log::warning('[MoMo Callback] Chữ ký không hợp lệ');
            return redirect()->route('user.orders.index')
                ->with('error', 'Phản hồi MoMo không hợp lệ. Vui lòng kiểm tra lại đơn hàng.');
        }

        if ($momo->isSuccessful($payload)) {
            // Hoàn thành thanh toán (idempotent — không trừ kho 2 lần)
            $this->completePayment($payload, $ghnOrders, $momo);

            return redirect()->route('user.orders.index')
                ->with('success', '🎉 Thanh toán MoMo thành công! Đơn hàng của bạn đang được xử lý.');
        }

        // Thất bại / Bị hủy
        $this->processFailedPayment($payload, $momo);

        $resultCode = $payload['resultCode'] ?? '?';
        return redirect()->route('user.orders.index')
            ->with('error', "Thanh toán MoMo thất bại (mã: {$resultCode}). Tồn kho đã được hoàn lại.");
    }

    // ==========================================
    // IPN — Server-to-server từ MoMo
    // ==========================================

    /**
     * MoMo gọi IPN URL để thông báo kết quả giao dịch (tin cậy nhất).
     * KHÔNG cần user ở đây — xử lý nền hoàn toàn.
     */
    public function ipn(Request $request, GHNOrderService $ghnOrders, MomoService $momo)
    {
        $payload = $request->all();
        Log::info('[MoMo IPN] Nhận request', ['resultCode' => $payload['resultCode'] ?? 'N/A', 'orderId' => $payload['orderId'] ?? '']);

        if ($momo->isValidSuccessfulResponse($payload)) {
            $this->completePayment($payload, $ghnOrders, $momo);
        } elseif ($momo->isValidResponse($payload)) {
            $this->processFailedPayment($payload, $momo);
        } else {
            Log::warning('[MoMo IPN] Chữ ký không hợp lệ');
        }

        return response()->json(['message' => 'Received']);
    }

    // ==========================================
    // PRIVATE HELPERS
    // ==========================================

    /** Tạo bản ghi PaymentTransaction mới cho đơn hàng MoMo. */
    private function newTransaction(Order $order): PaymentTransaction
    {
        return PaymentTransaction::create([
            'order_id' => $order->id,
            'gateway'  => 'momo',
            'amount'   => $order->total_price,
            'status'   => 'pending',
        ]);
    }

    /** Gọi MomoService tạo URL và redirect sang MoMo. */
    private function redirectToMomo(Order $order, PaymentTransaction $transaction, MomoService $momo)
    {
        $result = $momo->createPayment($order, $transaction);

        if (isset($result['payUrl'])) {
            return redirect()->away($result['payUrl']);
        }

        Log::error('[MoMo] Không có payUrl trong response', ['response' => $result]);
        return redirect()->route('user.orders.index')
            ->with('error', 'Không thể kết nối tới MoMo. Vui lòng thử lại hoặc chọn COD.');
    }

    /**
     * Cập nhật DB sau khi MoMo báo thanh toán thành công.
     * Idempotent: kiểm tra status = 'pending' trước khi cập nhật.
     * Sau đó tạo vận đơn GHN.
     */
    private function completePayment(array $payload, GHNOrderService $ghnOrders, MomoService $momo): string
    {
        $result = DB::transaction(function () use ($payload, $momo) {
            $transaction = PaymentTransaction::where('gateway', 'momo')
                ->where('gateway_order_id', $payload['orderId'] ?? '')
                ->lockForUpdate()
                ->first();

            if (!$transaction) {
                Log::error('[MoMo] Không tìm thấy transaction', ['orderId' => $payload['orderId'] ?? '']);
                return 'not_found';
            }

            // Đã xử lý rồi — idempotent
            if ($transaction->status === 'paid') {
                return 'already_paid';
            }

            $order = Order::lockForUpdate()->find($transaction->order_id);
            if (!$order) return 'order_not_found';

            // Kiểm tra số tiền khớp
            if ((int) $transaction->amount !== (int) ($payload['amount'] ?? 0)) {
                Log::error('[MoMo] Số tiền không khớp', [
                    'expected' => $transaction->amount,
                    'received' => $payload['amount'] ?? 0,
                ]);
                $momo->markFailed($transaction, $payload);
                return 'amount_mismatch';
            }

            // Cập nhật Order và Transaction
            $order->update(['status' => 'paid', 'shipping_status' => 'processing']);
            $momo->markPaid($transaction, $payload);

            Log::info('[MoMo] Đơn hàng đã thanh toán thành công', ['orderId' => $order->id]);

            return ['ok', $order->id];
        });

        if (!is_array($result)) {
            return (string) $result;
        }

        // Load Order cùng quan hệ để gửi Mail và tạo GHN
        $order = Order::with('items.product', 'user', 'paymentTransactions')->find($result[1]);

        // Gửi Email Xác Nhận cho đơn MoMo (nếu kết nối SMTP hoạt động)
        if ($this->canSendSmtp()) {
            try {
                if ($order && $order->user) {
                    \Illuminate\Support\Facades\Mail::to($order->user->email)->send(new \App\Mail\OrderConfirmation($order));
                }
            } catch (\Exception $e) {
                Log::error('[Email] Lỗi gửi email xác nhận đơn hàng (MoMo): ' . $e->getMessage());
            }
        }

        try {
            $ghnResponse = $ghnOrders->create($order, true);
            if (isset($ghnResponse['code']) && $ghnResponse['code'] === 200) {
                $order->update([
                    'ghn_order_code'  => $ghnResponse['data']['order_code'],
                    'shipping_status' => 'ready_to_pick',
                ]);
                Log::info('[GHN] Tạo vận đơn thành công', ['code' => $ghnResponse['data']['order_code']]);
                return 'created';
            }

            Log::warning('[GHN] Tạo vận đơn thất bại sau khi MoMo TT', ['response' => $ghnResponse]);
            $order->update(['shipping_status' => 'pending']);
        } catch (\Exception $e) {
            Log::error('[GHN] Exception khi tạo vận đơn: ' . $e->getMessage());
            $order->update(['shipping_status' => 'pending']);
        }

        return 'ghn_failed';
    }

    /**
     * Xử lý thanh toán thất bại:
     * - Mark transaction failed
     * - Hoàn lại tồn kho
     * - Hủy đơn hàng
     */
    private function processFailedPayment(array $payload, MomoService $momo): void
    {
        $transaction = PaymentTransaction::where('gateway', 'momo')
            ->where('gateway_order_id', $payload['orderId'] ?? '')
            ->where('status', 'pending')
            ->first();

        if (!$transaction) return; // Đã xử lý hoặc không tồn tại

        DB::transaction(function () use ($transaction, $payload, $momo) {
            $momo->markFailed($transaction, $payload);

            $order = Order::with('items')->find($transaction->order_id);
            if (!$order || $order->status !== 'pending') return;

            // Hoàn lại tồn kho
            foreach ($order->items as $item) {
                Product::where('id', $item->product_id)->increment('stock_quantity', $item->quantity);
                if ($item->variant_id) {
                    ProductVariant::where('id', $item->variant_id)->increment('stock', $item->quantity);
                }
            }

            $order->update([
                'status'          => 'cancelled',
                'shipping_status' => 'cancelled',
            ]);

            Log::info('[MoMo] Đơn hàng bị hủy và tồn kho đã hoàn lại', ['orderId' => $order->id]);
        });
    }
}