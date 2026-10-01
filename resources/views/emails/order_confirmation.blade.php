<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận đơn hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #0d6efd;
            color: #ffffff;
            text-align: center;
            padding: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content p {
            margin-bottom: 15px;
        }
        .order-info {
            background-color: #f1f5f9;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }
        .order-info p {
            margin: 5px 0;
        }
        .order-info strong {
            display: inline-block;
            width: 150px;
        }
        .table-items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table-items th, .table-items td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }
        .table-items th {
            background-color: #f8f9fa;
        }
        .table-items td.text-right, .table-items th.text-right {
            text-align: right;
        }
        .summary {
            width: 100%;
            margin-top: 20px;
        }
        .summary td {
            padding: 8px 12px;
        }
        .summary td.label {
            text-align: right;
            font-weight: bold;
        }
        .summary td.value {
            text-align: right;
            width: 150px;
        }
        .total-row {
            font-size: 18px;
            color: #dc3545;
        }
        .footer {
            background-color: #f8f9fa;
            text-align: center;
            padding: 15px;
            font-size: 14px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
        }
        .btn-primary {
            display: inline-block;
            padding: 10px 20px;
            background-color: #0d6efd;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header">
            <h1>Cảm ơn bạn đã đặt hàng!</h1>
        </div>

        <!-- Content -->
        <div class="content">
            <p>Xin chào <strong>{{ $order->name }}</strong>,</p>
            <p>Cảm ơn bạn đã mua sắm tại <strong>WashingStore</strong>. Đơn hàng của bạn đã được ghi nhận và đang được xử lý.</p>

            <!-- Order Info -->
            <div class="order-info">
                <p><strong>Mã đơn hàng:</strong> #{{ $order->id }}</p>
                <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</p>
                <p><strong>Phương thức TT:</strong> 
                    @if($order->paymentTransactions->first())
                        {{ strtoupper($order->paymentTransactions->first()->gateway) }}
                    @else
                        COD
                    @endif
                </p>
                <p><strong>Giao hàng đến:</strong> {{ $order->address }}</p>
                <p><strong>Số điện thoại:</strong> {{ $order->phone }}</p>
            </div>

            <!-- Items -->
            <h3>Chi tiết đơn hàng</h3>
            <table class="table-items">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-right" style="width: 80px;">SL</th>
                        <th class="text-right" style="width: 120px;">Đơn giá</th>
                        <th class="text-right" style="width: 120px;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    @php $subtotal = 0; @endphp
                    @foreach($order->items as $item)
                        @php 
                            $lineTotal = $item->price * $item->quantity;
                            $subtotal += $lineTotal;
                        @endphp
                        <tr>
                            <td>
                                {{ $item->product->name ?? 'Sản phẩm' }}
                                @if($item->variant_id)
                                    <br><small style="color: #6c757d;">Màu: {{ $item->variant->color_name ?? '' }}</small>
                                @endif
                            </td>
                            <td class="text-right">{{ $item->quantity }}</td>
                            <td class="text-right">{{ number_format($item->price, 0, ',', '.') }}đ</td>
                            <td class="text-right">{{ number_format($lineTotal, 0, ',', '.') }}đ</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Summary -->
            <table class="summary">
                <tr>
                    <td class="label">Tạm tính:</td>
                    <td class="value">{{ number_format($subtotal, 0, ',', '.') }}đ</td>
                </tr>
                <tr>
                    <td class="label">Phí vận chuyển:</td>
                    <td class="value">
                        @if($order->ghn_total_fee > 0)
                            {{ number_format($order->ghn_total_fee, 0, ',', '.') }}đ
                        @else
                            Đang tính
                        @endif
                    </td>
                </tr>
                @if($order->discount_amount > 0)
                    <tr>
                        <td class="label" style="color: #198754;">Giảm giá:</td>
                        <td class="value" style="color: #198754;">- {{ number_format($order->discount_amount, 0, ',', '.') }}đ</td>
                    </tr>
                @endif
                <tr class="total-row">
                    <td class="label">Tổng thanh toán:</td>
                    <td class="value"><strong>{{ number_format($order->total_price, 0, ',', '.') }}đ</strong></td>
                </tr>
            </table>

            <div style="text-align: center;">
                <a href="{{ url('/') }}" class="btn-primary">Tiếp tục mua sắm</a>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>Email này được gửi tự động từ hệ thống của WashingStore. Vui lòng không trả lời email này.</p>
            <p>&copy; {{ date('Y') }} WashingStore. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
