@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Xử lý, Báo cáo giao dịch thanh toán</h2>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.finance.index') }}">Thống kê chỉ số</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="{{ route('admin.finance.transactions') }}">Giao dịch thanh toán</a>
        </li>
    </ul>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.finance.transactions') }}" method="GET" class="row g-3">
                <div class="col-md-2">
                    <label class="form-label fw-bold">Tìm kiếm</label>
                    <input type="text" name="search" class="form-control" placeholder="Mã ĐH, Tên, SĐT..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Từ ngày</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Đến ngày</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Phương thức</label>
                    <select name="method" class="form-select">
                        <option value="all" {{ request('method') == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="cod" {{ request('method') == 'cod' ? 'selected' : '' }}>COD</option>
                        <option value="momo" {{ request('method') == 'momo' ? 'selected' : '' }}>MoMo</option>
                        <option value="unknown" {{ request('method') == 'unknown' ? 'selected' : '' }}>Khác</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Trạng thái TT</label>
                    <select name="payment_status" class="form-select">
                        <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Lỗi / Thất bại</option>
                        <option value="cancelled" {{ request('payment_status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Sắp xếp</label>
                    <select name="sort" class="form-select">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá cao tới thấp</option>
                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá thấp tới cao</option>
                    </select>
                </div>
                <div class="col-12 text-end mt-3">
                    <a href="{{ route('admin.finance.transactions') }}" class="btn btn-secondary me-2">Xóa bộ lọc</a>
                    <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Table Transactions -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Phương thức</th>
                            <th>Số tiền</th>
                            <th>Thanh toán</th>
                            <th style="width: 200px;">Cập nhật (COD)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php
                                $statusInfo = $statuses[$order->payment_status] ?? ['label' => 'N/A', 'color' => 'secondary'];
                                $isCod = strtolower($order->payment_method) === 'cod';
                                $allowedTransitions = $isCod ? ($codTransitions[$order->payment_status] ?? []) : [];
                            @endphp
                            <tr>
                                <td>
                                    <strong>#{{ $order->id }}</strong><br>
                                    <small class="text-muted">{{ $order->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    {{ $order->name }}<br>
                                    <small class="text-muted"><i class="fa-solid fa-phone"></i> {{ $order->phone }}</small>
                                </td>
                                <td class="text-uppercase fw-bold text-primary">
                                    {{ $order->payment_method ?? 'N/A' }}
                                </td>
                                <td class="text-danger fw-bold">
                                    {{ number_format($order->total_price, 0, ',', '.') }} ₫
                                </td>
                                <td>
                                    <span class="badge bg-{{ $statusInfo['color'] }}">
                                        {{ $statusInfo['label'] }}
                                    </span>
                                </td>
                                <td>
                                    @if($isCod && !empty($allowedTransitions) || auth()->user()->role === 'super_admin')
                                        <form action="{{ route('admin.finance.update-status', $order->id) }}" method="POST" class="d-flex align-items-center">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="payment_id" value="{{ $order->payment_id }}">
                                            <select name="status" class="form-select form-select-sm me-2" required>
                                                <option value="">-- Đổi sang --</option>
                                                @foreach($allowedTransitions as $t)
                                                    <option value="{{ $t }}">{{ $statuses[$t]['label'] }}</option>
                                                @endforeach
                                                @if(auth()->user()->role === 'super_admin')
                                                    <option disabled>--- Admin ---</option>
                                                    @foreach($statuses as $k => $v)
                                                        @if($k !== $order->payment_status)
                                                            <option value="{{ $k }}">{{ $v['label'] }}</option>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-success" title="Cập nhật">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted fst-italic" style="font-size: 12px;">Không khả dụng</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">Không tìm thấy giao dịch nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
