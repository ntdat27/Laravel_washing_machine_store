@extends('admin.layout')

@section('content')

<style>
    /* ── STAT CARDS ── */
    .stat-card {
        border-radius: 20px;
        border: none;
        overflow: hidden;
        position: relative;
        transition: all .3s cubic-bezier(.25,.8,.25,1);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,.12) !important;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,.1);
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: -40px; right: 20px;
        width: 80px; height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
    }
    .stat-card .card-body { position: relative; z-index: 1; padding: 1.75rem 1.5rem; }
    .stat-label {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        opacity: .75;
        margin-bottom: .4rem;
    }
    .stat-value {
        font-size: 1.85rem;
        font-weight: 800;
        letter-spacing: -.5px;
        line-height: 1.1;
    }
    .stat-icon {
        position: absolute;
        top: 1.25rem;
        right: 1.25rem;
        font-size: 2.2rem;
        opacity: .25;
    }
    .stat-sub {
        font-size: .78rem;
        opacity: .7;
        margin-top: .6rem;
        display: flex;
        align-items: center;
        gap: .35rem;
        flex-wrap: wrap;
    }
    .stat-sub-pill {
        display: inline-flex; align-items: center; gap: .25rem;
        background: rgba(255,255,255,.2);
        border-radius: 50px;
        padding: .2rem .6rem;
        font-size: .72rem;
        font-weight: 600;
    }

    /* ── CHART CARD ── */
    .chart-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
    }
    .chart-header {
        padding: 1.5rem 1.75rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;
    }
    .chart-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0f172a;
        display: flex; align-items: center; gap: .5rem;
    }
    .chart-badge {
        display: inline-flex; align-items: center; gap: .3rem;
        font-size: .72rem; font-weight: 700;
        padding: .3rem .8rem;
        border-radius: 50px;
        background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe;
    }

    /* ── TOP PRODUCT TABLE ── */
    .top-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .top-table thead th {
        padding: .75rem 1rem;
        font-size: .72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #94a3b8;
        background: #f8faff;
        border-bottom: 1px solid #e2e8f0;
    }
    .top-table thead th:first-child { border-radius: 0; }
    .top-table tbody tr {
        transition: background .2s;
    }
    .top-table tbody tr:hover td { background: #f8faff; }
    .top-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        background: #fff;
    }
    .top-table tbody tr:last-child td { border-bottom: none; }
    .rank-circle {
        width: 28px; height: 28px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: .72rem; font-weight: 800;
        flex-shrink: 0;
    }
    .product-mini-img {
        width: 48px; height: 48px;
        border-radius: 10px;
        object-fit: contain;
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        border: 1px solid #e2e8f0;
        padding: 4px;
    }

    /* ── WELCOME BANNER ── */
    .welcome-banner {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #0ea5e9 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        color: #fff;
    }
    .welcome-banner::before {
        content: '\f7ed';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: -20px; top: -20px;
        font-size: 140px;
        opacity: .06;
        transform: rotate(12deg);
    }
    .welcome-banner h3 { font-weight: 800; font-size: 1.45rem; letter-spacing: -.3px; }
</style>

{{-- WELCOME BANNER --}}
<div class="welcome-banner mb-4">
    <div class="position-relative z-1">
        <div class="d-flex align-items-center gap-3 mb-2">
            <i class="fa-solid fa-gauge fa-lg opacity-75"></i>
            <span style="font-size:.78rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;opacity:.7;">Quản Trị Hệ Thống</span>
        </div>
        <h3 class="mb-1">Xin chào, {{ auth()->user()->name ?? 'Quản trị viên' }}! 👋</h3>
        <p class="mb-0" style="opacity:.7;font-size:.95rem;">Đây là tổng quan tình hình kinh doanh hôm nay — WashingStore Admin.</p>
    </div>
</div>

{{-- STAT CARDS --}}
<div class="row g-4 mb-4">

    {{-- Doanh thu --}}
    <div class="col-md-4">
        <div class="card stat-card shadow-sm" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);">
            <div class="card-body text-white">
                <i class="fa-solid fa-money-bill-wave stat-icon"></i>
                <div class="stat-label">Tổng Doanh Thu</div>
                <div class="stat-value">{{ number_format($totalRevenue / 1000000, 1) }}M</div>
                <div class="stat-sub">
                    <i class="fa-solid fa-chart-line fa-xs"></i>
                    <span>{{ number_format($totalRevenue, 0, ',', '.') }} đ</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Đơn hàng --}}
    <div class="col-md-4">
        <div class="card stat-card shadow-sm" style="background:linear-gradient(135deg,#16a34a,#15803d);">
            <div class="card-body text-white">
                <i class="fa-solid fa-box-open stat-icon"></i>
                <div class="stat-label">Tổng Đơn Hàng</div>
                <div class="stat-value">{{ number_format($totalOrders) }}</div>
                <div class="stat-sub">
                    <span class="stat-sub-pill"><i class="fa-solid fa-clock fa-xs"></i> {{ $pendingOrders }} chờ</span>
                    <span class="stat-sub-pill"><i class="fa-solid fa-truck fa-xs"></i> {{ $deliveringOrders }} giao</span>
                    <span class="stat-sub-pill"><i class="fa-solid fa-check fa-xs"></i> {{ $completedOrders }} xong</span>
                    <span class="stat-sub-pill"><i class="fa-solid fa-ban fa-xs"></i> {{ $cancelledOrders }} hủy</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Khách hàng --}}
    <div class="col-md-4">
        <div class="card stat-card shadow-sm" style="background:linear-gradient(135deg,#7c3aed,#6d28d9);">
            <div class="card-body text-white">
                <i class="fa-solid fa-users stat-icon"></i>
                <div class="stat-label">Khách Hàng</div>
                <div class="stat-value">{{ number_format($totalCustomers) }}</div>
                <div class="stat-sub">
                    <i class="fa-solid fa-user-plus fa-xs"></i>
                    <span>Người dùng đã đăng ký</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- REVENUE CHART --}}
<div class="chart-card mb-4">
    <div class="chart-header">
        <div class="chart-title">
            <i class="fa-solid fa-chart-area text-primary"></i>
            Biểu Đồ Doanh Thu
        </div>
        <div class="chart-badge">
            <i class="fa-regular fa-calendar"></i> 30 ngày gần nhất
        </div>
    </div>
    <div class="p-4">
        <canvas id="revenueChart" height="80"></canvas>
    </div>
</div>

{{-- TOP PRODUCTS --}}
<div class="chart-card">
    <div class="chart-header">
        <div class="chart-title">
            <i class="fa-solid fa-medal text-warning"></i>
            Top 5 Sản Phẩm Bán Chạy
        </div>
        <span class="chart-badge" style="background:#fef9c3;color:#92400e;border-color:#fde68a;">
            <i class="fa-solid fa-fire text-warning"></i> Hot sellers
        </span>
    </div>
    <div class="p-4">
        <div class="table-responsive">
            <table class="top-table">
                <thead>
                    <tr>
                        <th style="width:48px;">#</th>
                        <th>Sản phẩm</th>
                        <th class="text-center">Đã bán</th>
                        <th class="text-end">Doanh thu</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topProducts as $index => $product)
                        @php
                            $rankColors = [
                                0 => ['bg:#fef9c3;color:#92400e;border:1px solid #fde68a;', 'fa-crown text-warning'],
                                1 => ['bg:#f8fafc;color:#334155;border:1px solid #e2e8f0;', 'fa-medal text-secondary'],
                                2 => ['bg:#fff7ed;color:#9a3412;border:1px solid #fed7aa;', 'fa-medal text-orange'],
                            ];
                            [$rankStyle, $rankIcon] = $rankColors[$index] ?? ['background:#f1f5f9;color:#64748b;border:1px solid #e2e8f0;', 'fa-circle text-muted'];
                        @endphp
                        <tr>
                            <td>
                                <div class="rank-circle" style="{{ $rankStyle }}">
                                    @if($index < 3)
                                        <i class="fa-solid {{ $rankIcon }} fa-xs"></i>
                                    @else
                                        <span style="font-size:.72rem;font-weight:700;">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="{{ $product->image ? asset('storage/' . $product->image) : 'https://placehold.co/48' }}"
                                         alt="{{ $product->name }}" class="product-mini-img">
                                    <div>
                                        <div class="fw-700 text-dark" style="font-weight:700;font-size:.9rem;">{{ $product->name }}</div>
                                        <div class="text-muted" style="font-size:.78rem;">{{ number_format($product->price, 0, ',', '.') }} đ / sp</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge rounded-pill px-3 py-2" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;font-size:.8rem;font-weight:700;">
                                    {{ number_format($product->total_sold) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="fw-700 text-dark" style="font-weight:700;font-size:.92rem;">{{ number_format($product->total_revenue, 0, ',', '.') }} đ</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-5">
                                <i class="fa-solid fa-chart-simple fa-2x opacity-30 mb-2 d-block"></i>
                                Chưa có dữ liệu giao dịch
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('revenueChart').getContext('2d');

        const gradient = ctx.createLinearGradient(0, 0, 0, 360);
        gradient.addColorStop(0, 'rgba(37,99,235,0.25)');
        gradient.addColorStop(1, 'rgba(37,99,235,0.01)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($chartLabels) !!},
                datasets: [{
                    label: 'Doanh thu (đ)',
                    data: {!! json_encode($chartData) !!},
                    borderColor: '#2563eb',
                    backgroundColor: gradient,
                    borderWidth: 2.5,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#2563eb',
                    pointBorderWidth: 2.5,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: '#2563eb',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2.5,
                    fill: true,
                    tension: 0.42
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#94a3b8',
                        bodyColor: '#f8faff',
                        titleFont: { size: 12, weight: '500' },
                        bodyFont: { size: 14, weight: 'bold' },
                        padding: 14,
                        cornerRadius: 12,
                        callbacks: {
                            label: ctx => '  ' + ctx.raw.toLocaleString('vi-VN') + ' đ'
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8' }
                    },
                    y: {
                        beginAtZero: true,
                        border: { display: false, dash: [6, 4] },
                        grid: { color: '#f1f5f9', drawTicks: false },
                        ticks: {
                            padding: 14,
                            font: { size: 11, weight: '500' },
                            color: '#94a3b8',
                            callback: v => {
                                if (v === 0) return '0';
                                if (v >= 1000000) return (v / 1000000).toFixed(1) + 'M';
                                if (v >= 1000)    return (v / 1000) + 'K';
                                return v;
                            }
                        }
                    }
                },
                interaction: { mode: 'index', intersect: false }
            }
        });
    });
</script>

@endsection