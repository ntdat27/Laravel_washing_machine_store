@extends('admin.layout')
@section('title', 'Biểu đồ báo cáo doanh thu')

@section('content')
    <style>
        .chart-wrap {
            min-height: 360px;
        }

        .chart-wrap canvas {
            width: 100% !important;
            height: 360px !important;
        }
    </style>
    <div class="container-fluid">
        <h2 class="mb-4">Biểu đồ báo cáo doanh thu</h2>

        <nav class="nav nav-pills mb-4" aria-label="Báo cáo">
            <a class="nav-link px-4 text-dark" href="{{ route('admin.reports.index') }}">Bảng số liệu</a>
            <a class="nav-link active fw-bold px-4" aria-current="page" href="{{ route('admin.reports.charts') }}">Biểu
                đồ</a>
        </nav>
        <p class="text-muted">Chỉ gồm đơn đã thanh toán, chưa hoàn tiền và không bị hủy hoặc hoàn hàng. Doanh thu tính theo
            ngày tạo đơn; số liệu theo danh mục không gồm phí vận chuyển.</p>

        <div id="report-chart-error" class="alert alert-danger d-none fw-bold" role="alert">
            Không tải được thư viện biểu đồ. Bạn có thể xem số liệu tại trang <a href="{{ route('admin.reports.index') }}"
                class="alert-link">Bảng số liệu</a>.
        </div>

        <!-- Các khung biểu đồ -->
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-3 pb-2"><strong class="fs-5">Doanh thu theo danh mục</strong></div>
                    <div class="card-body chart-wrap"><canvas id="categoryRevenueChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-3 pb-2"><strong class="fs-5">Doanh thu theo ngày (30 ngày)</strong>
                    </div>
                    <div class="card-body chart-wrap"><canvas id="revenueByDateChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-3 pb-2"><strong class="fs-5">Doanh thu theo tháng (12
                            tháng)</strong></div>
                    <div class="card-body chart-wrap"><canvas id="revenueByMonthChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-white pt-3 pb-2"><strong class="fs-5">Doanh thu theo năm</strong></div>
                    <div class="card-body chart-wrap"><canvas id="revenueByYearChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white pt-3 pb-2"><strong class="fs-5">Doanh thu theo phương thức thanh
                            toán</strong></div>
                    <div class="card-body chart-wrap d-flex justify-content-center">
                        <div style="width: 400px; height: 400px;">
                            <canvas id="revenueByPaymentMethodChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Đổ dữ liệu Data từ Laravel qua JS -->
    <div id="report-chart-data" hidden data-chart-data="{{ json_encode([
        'catLabels' => $catLabels ?? [],
        'catRevenue' => $catRevenue ?? [],
        'revDateLabels' => $revDateLabels ?? [],
        'revDateData' => $revDateData ?? [],
        'revMonthLabels' => $revMonthLabels ?? [],
        'revMonthData' => $revMonthData ?? [],
        'revYearLabels' => $revYearLabels ?? [],
        'revYearData' => $revYearData ?? [],
        'paymentMethodLabels' => $paymentMethodLabels ?? [],
        'paymentMethodRevenue' => $paymentMethodRevenue ?? [],
    ]) }}"></div>

    <!-- Nhúng thư viện Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        window.addEventListener('DOMContentLoaded', () => {
            if (typeof Chart === 'undefined') {
                document.getElementById('report-chart-error').classList.remove('d-none');
                return;
            }

            // Lấy chuỗi JSON từ thẻ ẩn và Parse thành Object
            const rawData = document.getElementById('report-chart-data').getAttribute('data-chart-data');
            const reportData = JSON.parse(rawData);

            // Bóc tách dữ liệu
            const catLabels = reportData.catLabels;
            const catRevenue = reportData.catRevenue.map(Number);
            const revDateLabels = reportData.revDateLabels;
            const revDateData = reportData.revDateData.map(Number);
            const revMonthLabels = reportData.revMonthLabels;
            const revMonthData = reportData.revMonthData.map(Number);
            const revYearLabels = reportData.revYearLabels;
            const revYearData = reportData.revYearData.map(Number);
            const payLabels = reportData.paymentMethodLabels;
            const payRevenue = reportData.paymentMethodRevenue.map(Number);

            // Hàm tạo biểu đồ chung (Bar, Line)
            const mk = (el, type, labels, data, label) => new Chart(el, {
                type: type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        fill: type === 'line',
                        tension: 0.3,
                        backgroundColor: 'rgba(54, 162, 235, 0.6)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Vẽ 4 biểu đồ đầu
            mk(document.getElementById('categoryRevenueChart'), 'bar', catLabels, catRevenue, 'Doanh thu (VNĐ)');
            mk(document.getElementById('revenueByDateChart'), 'line', revDateLabels, revDateData, 'Doanh thu (VNĐ)');
            mk(document.getElementById('revenueByMonthChart'), 'bar', revMonthLabels, revMonthData, 'Doanh thu (VNĐ)');
            mk(document.getElementById('revenueByYearChart'), 'bar', revYearLabels, revYearData, 'Doanh thu (VNĐ)');

            // Vẽ biểu đồ tròn cho Phương thức thanh toán (MoMo, COD)
            new Chart(document.getElementById('revenueByPaymentMethodChart'), {
                type: 'pie',
                data: {
                    labels: payLabels,
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: payRevenue,
                        backgroundColor: ['#d63384', '#0d6efd'] // Hồng cánh sen cho MoMo, Xanh lam cho COD
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
@endsection