@extends('admin.layout')
@section('title', 'Báo cáo doanh thu')

@section('content')
    <div class="container-fluid">
        <h2 class="mb-4">Báo cáo doanh thu</h2>

        <nav class="nav nav-pills mb-4" aria-label="Báo cáo">
            <a class="nav-link active fw-bold px-4" aria-current="page" href="{{ route('admin.reports.index') }}">Bảng số
                liệu</a>
            <a class="nav-link px-4 text-dark" href="{{ route('admin.reports.charts') }}">Biểu đồ</a>
        </nav>
        <p class="text-muted">Doanh thu tính theo ngày tạo đơn, chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy
            hoặc hoàn hàng.</p>

        <!-- Các thẻ tổng quan -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card card-body shadow-sm border-0 h-100 text-center py-4">
                    <span class="text-muted fw-bold mb-2"><i class="fa-solid fa-box text-primary me-1"></i> Tổng số đơn
                        hàng</span>
                    <h2 class="mb-0 text-primary fw-bold">{{ number_format($totalOrders) }}</h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-body shadow-sm border-0 h-100 text-center py-4">
                    <span class="text-muted fw-bold mb-2"><i class="fa-solid fa-users text-info me-1"></i> Tổng số khách
                        hàng</span>
                    <h2 class="mb-0 text-info fw-bold">{{ number_format($totalCustomers) }}</h2>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card card-body shadow-sm border-0 h-100 text-center py-4">
                    <span class="text-muted fw-bold mb-2"><i class="fa-solid fa-money-bill-wave text-success me-1"></i> Tổng
                        doanh thu (Gồm Ship)</span>
                    <h2 class="mb-0 text-success fw-bold">{{ number_format($totalRevenue, 0, ',', '.') }} đ</h2>
                </div>
            </div>
        </div>

        <!-- Bảng: Doanh thu theo danh mục -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white pt-3 pb-2">
                <strong class="fs-5">Doanh thu theo danh mục</strong>
                <div class="small text-muted mt-1">Tính theo giá sản phẩm khi đặt hàng, không gồm phí vận chuyển.</div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Danh mục</th>
                            <th class="text-end">Số lượng bán</th>
                            <th class="text-end">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryRevenue as $revenue)
                            <tr>
                                <td class="fw-bold">{{ $revenue->category_name ?? ('Danh mục #' . $revenue->category_id) }}</td>
                                <td class="text-end">{{ number_format($revenue->total_qty) }}</td>
                                <td class="text-end text-success fw-bold">
                                    {{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bảng: Doanh thu theo Ngày / Tháng / Năm -->
        @foreach([
                ['Doanh thu theo ngày', 'Ngày', 'date', $revenueByDate, 'd/m/Y'],
                ['Doanh thu theo tháng', 'Tháng', 'month', $revenueByMonth, 'm/Y'],
                ['Doanh thu theo năm', 'Năm', 'year', $revenueByYear, null],
            ] as [$title, $label, $field, $rows, $format])
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white pt-3 pb-2"><strong class="fs-5">{{ $title }}</strong></div>
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>{{ $label }}</th>
                                <th class="text-end">Số đơn đã thanh toán</th>
                                <th class="text-end">Doanh thu</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rows as $revenue)
                                <tr>
                                    <td class="fw-bold">
                                        {{ $format ? \Carbon\Carbon::parse($revenue->{$field} . ($field === 'month' ? '-01' : ''))->format($format) : $revenue->{$field} }}
                                    </td>
                                    <td class="text-end">{{ number_format($revenue->order_count) }}</td>
                                    <td class="text-end text-success fw-bold">
                                        {{ number_format($revenue->total_revenue, 0, ',', '.') }} đ</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">Chưa có doanh thu.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@endsection