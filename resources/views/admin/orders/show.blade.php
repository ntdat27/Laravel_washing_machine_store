@extends('admin.layout')
@section('title', 'Chi tiết đơn hàng')

@section('content')
    <div class="container-fluid">
        {{-- Thông báo --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Chi tiết đơn hàng <span class="text-primary">#DH{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span></h2>
            <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary shadow-sm">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </div>

        <div class="row g-4">
            {{-- Thông tin khách hàng --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-3 pb-2">
                        <strong class="fs-5"><i class="fa-solid fa-user me-2 text-primary"></i>Người nhận</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0 small">
                            <tr>
                                <td style="width: 110px" class="fw-bold text-muted">Họ tên:</td>
                                <td class="fw-bold">{{ $order->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Số điện thoại:</td>
                                <td>{{ $order->phone }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Địa chỉ:</td>
                                <td>{{ $order->address }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Tài khoản:</td>
                                <td>{{ $order->user?->email ?? 'Khách lẻ' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Trạng thái đơn --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-3 pb-2">
                        <strong class="fs-5"><i class="fa-solid fa-receipt me-2 text-warning"></i>Trạng thái giao dịch</strong>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless mb-0 small">
                            <tr>
                                <td style="width: 130px" class="fw-bold text-muted">Tổng thanh toán:</td>
                                <td><span class="text-danger fw-bold fs-5">{{ number_format($order->total_price, 0, ',', '.') }} đ</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Trạng thái đơn:</td>
                                <td><span class="badge bg-dark">{{ strtoupper($order->status) }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Trạng thái ship:</td>
                                <td>
                                    @php
                                        $ssColors = ['delivered' => 'success', 'cancelled' => 'danger', 'delivering' => 'warning'];
                                        $ssColor = $ssColors[$order->shipping_status] ?? 'info';
                                    @endphp
                                    <span class="badge bg-{{ $ssColor }}">{{ $order->shipping_status }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Mã vận đơn GHN:</td>
                                <td><span class="text-primary fw-bold">{{ $order->ghn_order_code ?? 'Chưa phát sinh' }}</span></td>
                            </tr>
                            <tr>
                                <td class="fw-bold text-muted">Ngày đặt:</td>
                                <td>{{ $order->created_at->format('d/m/Y H:i:s') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Panel cập nhật trạng thái --}}
            <div class="col-md-4">
                <div class="card shadow-sm border-0 h-100 border-start border-4 border-primary">
                    <div class="card-header bg-white pt-3 pb-2">
                        <strong class="fs-5"><i class="fa-solid fa-pen-to-square me-2 text-primary"></i>Cập nhật trạng thái</strong>
                    </div>
                    <div class="card-body">
                        @if(count($allowedTransitions) > 0)
                            @php
                                $transitionLabels = [
                                    'not_shipped'         => 'Chưa giao hàng',
                                    'processing'          => 'Đang xử lý / Đóng gói',
                                    'ready_to_pick'       => 'Sẵn sàng lấy hàng',
                                    'picking'             => 'Đang lấy hàng',
                                    'picked'              => 'Đã lấy hàng',
                                    'storing'             => 'Đang lưu kho',
                                    'transporting'        => 'Đang trung chuyển',
                                    'sorting'             => 'Đang phân loại',
                                    'delivering'          => 'Đang giao hàng',
                                    'delivered'           => '✓ Giao hàng thành công',
                                    'return'              => 'Chờ hoàn hàng',
                                    'returning'           => 'Đang hoàn hàng',
                                    'return_transporting' => 'Đang chuyển hoàn',
                                    'return_sorting'      => 'Đang phân loại hoàn',
                                    'returned'            => 'Đã hoàn hàng',
                                    'cancelled'           => '✕ Hủy đơn & hoàn kho',
                                ];
                            @endphp
                            <form action="{{ route('admin.orders.update', $order->id) }}" method="POST" id="statusForm">
                                @csrf
                                @method('PUT')
                                <div class="mb-3">
                                    <label class="form-label fw-bold small text-muted">CHUYỂN SANG TRẠNG THÁI:</label>
                                    <div class="d-flex flex-column gap-2">
                                        @foreach($allowedTransitions as $status)
                                            @php
                                                $isCancelBtn = $status === 'cancelled';
                                            @endphp
                                            <div class="form-check">
                                                <input class="form-check-input status-radio" type="radio"
                                                       name="shipping_status" id="status_{{ $status }}"
                                                       value="{{ $status }}"
                                                       data-is-cancel="{{ $isCancelBtn ? '1' : '0' }}">
                                                <label class="form-check-label {{ $isCancelBtn ? 'text-danger fw-bold' : '' }}"
                                                       for="status_{{ $status }}">
                                                    {{ $transitionLabels[$status] ?? $status }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="button" id="btnConfirmUpdate"
                                        class="btn btn-primary w-100 fw-bold rounded-pill" disabled>
                                    <i class="fa-solid fa-floppy-disk me-2"></i>Xác nhận cập nhật
                                </button>
                            </form>
                        @else
                            <div class="text-center py-4">
                                <i class="fa-solid fa-lock fa-2x text-muted mb-2"></i>
                                <p class="text-muted mb-0">Đơn hàng đã ở trạng thái cuối cùng.<br>Không thể thay đổi thêm.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Danh sách sản phẩm --}}
        <div class="card shadow-sm border-0 mb-4 mt-4">
            <div class="card-header bg-white pt-3 pb-2">
                <strong class="fs-5"><i class="fa-solid fa-cart-shopping me-2 text-success"></i>Sản phẩm đã mua</strong>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px">Ảnh</th>
                            <th>Tên sản phẩm</th>
                            <th>Variant / Màu sắc</th>
                            <th class="text-center">Số lượng</th>
                            <th class="text-end">Đơn giá</th>
                            <th class="text-end">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    @php $img = $item->variant?->image ?? $item->product?->image ?? null; @endphp
                                    @if($img)
                                        <img src="{{ asset('storage/' . $img) }}" class="rounded shadow-sm"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fa-solid fa-shirt text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-bold">{{ $item->product?->name ?? 'Sản phẩm đã bị xóa' }}</td>
                                <td>
                                    @if($item->variant)
                                        <div class="d-flex align-items-center gap-2">
                                            @if($item->variant->color_code)
                                                <span class="rounded-circle border d-inline-block"
                                                      style="width: 18px; height: 18px; background: {{ $item->variant->color_code }}"></span>
                                            @endif
                                            <span class="badge bg-light text-dark border">{{ $item->variant->color_name }}</span>
                                            <small class="text-muted">SKU: {{ $item->variant->sku }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-center fw-bold text-danger">×{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->price, 0, ',', '.') }} đ</td>
                                <td class="text-end text-success fw-bold">
                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="5" class="text-end fw-bold">Tổng cộng:</td>
                            <td class="text-end text-danger fw-bold fs-5">
                                {{ number_format($order->total_price, 0, ',', '.') }} đ
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Lịch sử thanh toán --}}
        @if($order->paymentTransactions->isNotEmpty())
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white pt-3 pb-2">
                    <strong class="fs-5"><i class="fa-solid fa-money-check-dollar me-2 text-info"></i>Lịch sử thanh toán</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle small">
                        <thead class="table-light">
                            <tr>
                                <th>Cổng</th><th>Số tiền</th><th>Trạng thái</th>
                                <th>Mã giao dịch</th><th>Thời điểm thanh toán</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->paymentTransactions as $tx)
                                <tr>
                                    <td><span class="badge bg-dark">{{ strtoupper($tx->gateway) }}</span></td>
                                    <td class="fw-bold text-danger">{{ number_format($tx->amount, 0, ',', '.') }} đ</td>
                                    <td>
                                        <span class="badge bg-{{ $tx->status === 'paid' ? 'success' : ($tx->status === 'failed' ? 'danger' : 'warning text-dark') }}">
                                            {{ strtoupper($tx->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted font-monospace">{{ $tx->transaction_id ?? '—' }}</td>
                                    <td>{{ $tx->paid_at ? $tx->paid_at->format('d/m/Y H:i:s') : '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    {{-- Script SweetAlert confirm cập nhật trạng thái --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radios   = document.querySelectorAll('.status-radio');
            const btnUpdate = document.getElementById('btnConfirmUpdate');
            const form     = document.getElementById('statusForm');

            if (!btnUpdate) return;

            radios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    btnUpdate.disabled = false;
                    const isCancel = this.getAttribute('data-is-cancel') === '1';
                    if (isCancel) {
                        btnUpdate.className = 'btn btn-danger w-100 fw-bold rounded-pill';
                        btnUpdate.innerHTML = '<i class="fa-solid fa-ban me-2"></i>Xác nhận HỦY & hoàn kho';
                    } else {
                        btnUpdate.className = 'btn btn-primary w-100 fw-bold rounded-pill';
                        btnUpdate.innerHTML = '<i class="fa-solid fa-floppy-disk me-2"></i>Xác nhận cập nhật';
                    }
                });
            });

            btnUpdate.addEventListener('click', function () {
                const selected = document.querySelector('.status-radio:checked');
                if (!selected) return;

                const isCancel  = selected.getAttribute('data-is-cancel') === '1';
                const newStatus = selected.value;

                Swal.fire({
                    title: isCancel ? '⚠️ Xác nhận hủy đơn?' : 'Xác nhận cập nhật?',
                    html: isCancel
                        ? `Đơn hàng sẽ bị <strong class="text-danger">hủy</strong> và tồn kho sẽ được <strong class="text-success">hoàn lại tự động</strong>.`
                        : `Chuyển trạng thái sang: <strong>${newStatus}</strong>`,
                    icon: isCancel ? 'warning' : 'question',
                    showCancelButton: true,
                    confirmButtonColor: isCancel ? '#dc3545' : '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: isCancel ? 'Hủy đơn & hoàn kho' : 'Xác nhận',
                    cancelButtonText: 'Không',
                    reverseButtons: true,
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
@endsection