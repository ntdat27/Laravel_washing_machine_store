<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * MomoService — Tích hợp MoMo Payment Gateway v2
 *
 * Tài liệu sandbox: https://developers.momo.vn/v3/docs/payment/api/payment-api
 * Sandbox endpoint: https://test-payment.momo.vn/v2/gateway/api/create
 * requestType captureWallet = QR + App MoMo (phổ biến nhất)
 * requestType payWithMethod  = Tất cả phương thức (ATM, thẻ, QR...)
 */
class MomoService
{
    private string $endpoint;
    private string $partnerCode;
    private string $accessKey;
    private string $secretKey;
    private string $partnerName;
    private bool   $verifySSL;

    public function __construct()
    {
        $this->endpoint    = config('services.momo.endpoint',     'https://test-payment.momo.vn/v2/gateway/api/create');
        $this->partnerCode = config('services.momo.partner_code', '');
        $this->accessKey   = config('services.momo.access_key',   '');
        $this->secretKey   = config('services.momo.secret_key',   '');
        $this->partnerName = config('services.momo.partner_name', 'WashingStore');
        $this->verifySSL   = (bool) config('services.momo.verify_ssl', false);
    }

    // ==========================================
    // TẠO GIAO DỊCH — Gửi yêu cầu sang MoMo API
    // ==========================================

    /**
     * Tạo giao dịch MoMo và trả về kết quả (bao gồm payUrl).
     *
     * @return array   Response JSON từ MoMo (có 'payUrl' nếu thành công)
     */
    public function createPayment(Order $order, PaymentTransaction $transaction): array
    {
        $orderId     = $order->id . '_' . $transaction->id . '_' . time();
        $requestId   = (string) time();
        $amount      = (string) ((int) $order->total_price);
        $orderInfo   = 'Thanh toan don hang #DH' . str_pad($order->id, 6, '0', STR_PAD_LEFT);
        $extraData   = (string) $order->id;          // Dùng để tra orderId khi callback
        $requestType = 'payWithATM';               // Thẻ nội địa/quốc tế

        $redirectUrl = config('services.momo.redirect_url') ?: route('user.payment.momo.callback');
        $ipnUrl      = config('services.momo.ipn_url')      ?: route('payment.momo.ipn');

        // Chuỗi hash theo thứ tự alphabetical MoMo yêu cầu
        $rawHash = 'accessKey='  . $this->accessKey
                 . '&amount='    . $amount
                 . '&extraData=' . $extraData
                 . '&ipnUrl='    . $ipnUrl
                 . '&orderId='   . $orderId
                 . '&orderInfo=' . $orderInfo
                 . '&partnerCode=' . $this->partnerCode
                 . '&redirectUrl=' . $redirectUrl
                 . '&requestId=' . $requestId
                 . '&requestType=' . $requestType;

        $signature = hash_hmac('sha256', $rawHash, $this->secretKey);

        $payload = [
            'partnerCode'  => $this->partnerCode,
            'partnerName'  => $this->partnerName,
            'storeId'      => $this->partnerCode,
            'requestId'    => $requestId,
            'amount'       => $amount,
            'orderId'      => $orderId,
            'orderInfo'    => $orderInfo,
            'redirectUrl'  => $redirectUrl,
            'ipnUrl'       => $ipnUrl,
            'lang'         => 'vi',
            'extraData'    => $extraData,
            'requestType'  => $requestType,
            'signature'    => $signature,
        ];

        // Lưu gateway_order_id và request_payload vào DB
        $transaction->update([
            'gateway_order_id' => $orderId,
            'request_payload'  => $payload,
        ]);

        Log::info('[MoMo] Tạo giao dịch', ['orderId' => $orderId, 'amount' => $amount, 'signature' => $signature]);

        $response = Http::withOptions(['verify' => $this->verifySSL])
            ->post($this->endpoint, $payload);

        $result = $response->json() ?? [];

        // Cập nhật trạng thái transaction, giới hạn message 250 ký tự để tránh lỗi DB
        $transaction->update([
            'response_payload' => $result,
            'result_code'      => isset($result['resultCode']) ? (int) $result['resultCode'] : null,
            'message'          => isset($result['message']) ? Str::limit($result['message'], 250) : null,
            'status'           => isset($result['payUrl']) ? 'initiated' : 'failed',
        ]);

        if (!isset($result['payUrl'])) {
            Log::error('[MoMo] Không nhận được payUrl', ['response' => $result]);
        }

        return $result;
    }

    // ==========================================
    // VERIFY & XỬ LÝ KẾT QUẢ TỪ MOMO
    // ==========================================

    /**
     * Kiểm tra chữ ký từ MoMo gửi về (callback/IPN).
     * Theo tài liệu MoMo v2 — field signature được tính từ các trường cố định.
     */
    public function isValidResponse(array $payload): bool
    {
        if (!isset($payload['signature'])) {
            return false;
        }

        $rawHash = 'accessKey='    . $this->accessKey
                 . '&amount='      . ($payload['amount']       ?? '')
                 . '&extraData='   . ($payload['extraData']    ?? '')
                 . '&message='     . ($payload['message']      ?? '')
                 . '&orderId='     . ($payload['orderId']       ?? '')
                 . '&orderInfo='   . ($payload['orderInfo']    ?? '')
                 . '&orderType='   . ($payload['orderType']    ?? '')
                 . '&partnerCode=' . ($payload['partnerCode']  ?? '')
                 . '&payType='     . ($payload['payType']      ?? '')
                 . '&requestId='   . ($payload['requestId']    ?? '')
                 . '&responseTime='. ($payload['responseTime'] ?? '')
                 . '&resultCode='  . ($payload['resultCode']   ?? '')
                 . '&transId='     . ($payload['transId']      ?? '');

        $expected = hash_hmac('sha256', $rawHash, $this->secretKey);

        return hash_equals($expected, (string) $payload['signature']);
    }

    /**
     * Giao dịch hợp lệ VÀ thành công (resultCode === 0).
     */
    public function isValidSuccessfulResponse(array $payload): bool
    {
        return $this->isValidResponse($payload) && $this->isSuccessful($payload);
    }

    /**
     * Kiểm tra resultCode = '0' (thành công).
     */
    public function isSuccessful(array $payload): bool
    {
        return (string) ($payload['resultCode'] ?? '-1') === '0';
    }

    // ==========================================
    // CẬP NHẬT TRẠNG THÁI TRANSACTION
    // ==========================================

    public function markPaid(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id'   => $payload['transId']     ?? null,
            'result_code'      => (int) ($payload['resultCode'] ?? 0),
            'message'          => isset($payload['message']) ? Str::limit($payload['message'], 250) : null,
            'response_payload' => $payload,
            'status'           => 'paid',
            'paid_at'          => Carbon::now(),
        ]);
    }

    public function markFailed(PaymentTransaction $transaction, array $payload): void
    {
        $transaction->update([
            'transaction_id'   => $payload['transId']    ?? null,
            'result_code'      => isset($payload['resultCode']) ? (int) $payload['resultCode'] : null,
            'message'          => isset($payload['message']) ? Str::limit($payload['message'], 250) : null,
            'response_payload' => $payload,
            'status'           => 'failed',
        ]);
    }

    /**
     * Lấy order_id gốc từ extraData trong payload MoMo callback.
     */
    public function getOrderId(array $payload): ?int
    {
        $extraData = $payload['extraData'] ?? null;
        return is_numeric($extraData) ? (int) $extraData : null;
    }
}