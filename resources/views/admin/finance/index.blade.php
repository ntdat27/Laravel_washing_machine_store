@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Xử lý, Báo cáo giao dịch thanh toán</h2>
    </div>

    <!-- Navigation Tabs -->
    <ul class="nav nav-tabs mb-4">
        <li class="nav-item">
            <a class="nav-link active fw-bold" href="{{ route('admin.finance.index') }}">Thống kê chỉ số</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.finance.transactions') }}">Giao dịch thanh toán</a>
        </li>
    </ul>

    <!-- Filter Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="{{ route('admin.finance.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Tìm kiếm (Mã ĐH, Tên, SĐT)</label>
                    <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa..." value="{{ request('search') }}">
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
                        <option value="unknown" {{ request('method') == 'unknown' ? 'selected' : '' }}>Không xác định</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Trạng thái thanh toán</label>
                    <select name="payment_status" class="form-select">
                        <option value="all" {{ request('payment_status') == 'all' ? 'selected' : '' }}>Tất cả</option>
                        <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh toán</option>
                        <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán</option>
                        <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Lỗi / Thất bại</option>
                        <option value="cancelled" {{ request('payment_status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Đã hoàn tiền</option>
                    </select>
                </div>
                <div class="col-12 text-end mt-3">
                    <a href="{{ route('admin.finance.index') }}" class="btn btn-secondary me-2">Xóa bộ lọc</a>
                    <button type="submit" class="btn btn-primary">Áp dụng bộ lọc</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title">Tổng giá trị đơn hàng</h5>
                    <h3 class="fw-bold">{{ number_format($statusTotals['total_amount'], 0, ',', '.') }} ₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title">Đã thanh toán</h5>
                    <h3 class="fw-bold">{{ number_format($statusTotals['paid'], 0, ',', '.') }} ₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-dark bg-warning h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title">Chờ xử lý (COD)</h5>
                    <h3 class="fw-bold">{{ number_format($statusTotals['cod_pending'], 0, ',', '.') }} ₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title">Đang chờ MoMo</h5>
                    <h3 class="fw-bold">{{ number_format($statusTotals['momo_pending'], 0, ',', '.') }} ₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-danger h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title">Thanh toán thất bại</h5>
                    <h3 class="fw-bold">{{ number_format($statusTotals['failed'], 0, ',', '.') }} ₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-secondary h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title">Đã hủy</h5>
                    <h3 class="fw-bold">{{ number_format($statusTotals['cancelled'], 0, ',', '.') }} ₫</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-dark h-100 shadow">
                <div class="card-body">
                    <h5 class="card-title">Đã hoàn tiền</h5>
                    <h3 class="fw-bold">{{ number_format($statusTotals['refunded'], 0, ',', '.') }} ₫</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Table by Method -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <h5 class="mb-0">Thống kê theo phương thức</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Phương thức</th>
                        <th>Số lượng đơn</th>
                        <th>Tổng giá trị</th>
                        <th>Đã thanh toán</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($methodTotals as $method => $data)
                        <tr>
                            <td class="text-uppercase fw-bold">{{ $method }}</td>
                            <td>{{ number_format($data['count']) }}</td>
                            <td class="text-primary fw-bold">{{ number_format($data['total'], 0, ',', '.') }} ₫</td>
                            <td class="text-success fw-bold">{{ number_format($data['paid_total'], 0, ',', '.') }} ₫</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
