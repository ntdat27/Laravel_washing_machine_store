@extends('admin.layout')
@section('title', 'Quản lý Đơn hàng')

@section('content')

<style>
    .admin-card { background:#fff;border:1px solid #e2e8f0;border-radius:20px;overflow:hidden; }
    .admin-table { width:100%;border-collapse:separate;border-spacing:0; }
    .admin-table thead th { padding:.7rem 1rem;font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#94a3b8;background:#f8faff;border-bottom:1px solid #e2e8f0; }
    .admin-table tbody td { padding:.85rem 1rem;border-bottom:1px solid #f1f5f9;vertical-align:middle;font-size:.88rem;background:#fff; }
    .admin-table tbody tr:last-child td { border-bottom:none; }
    .admin-table tbody tr:hover td { background:#f8faff; }

    /* ── TABS ── */
    .admin-tabs {
        display: flex;
        gap: .25rem;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.5rem;
        overflow-x: auto;
        padding-bottom: 0;
    }
    .admin-tab {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .6rem 1.1rem;
        border-radius: 10px 10px 0 0;
        font-size: .85rem; font-weight: 600;
        color: #64748b;
        text-decoration: none;
        border: 1px solid transparent;
        border-bottom: none;
        transition: all .2s;
        white-space: nowrap;
        margin-bottom: -2px;
    }
    .admin-tab:hover { color: #2563eb; background: #f8faff; }
    .admin-tab.active {
        color: #2563eb;
        background: #fff;
        border-color: #e2e8f0;
        border-bottom-color: #fff;
        box-shadow: 0 -2px 0 #2563eb inset;
    }
    .tab-count {
        display: inline-flex; align-items: center;
        padding: .15rem .5rem;
        border-radius: 50px;
        font-size: .7rem; font-weight: 700;
    }

    /* ── STATUS BADGES ── */
    .pay-badge {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .25rem .7rem; border-radius: 50px;
        font-size: .72rem; font-weight: 700;
    }
    .pay-badge.paid     { background:#f0fdf4;color:#166534;border:1px solid #bbf7d0; }
    .pay-badge.pending  { background:#fefce8;color:#854d0e;border:1px solid #fde68a; }
    .pay-badge.cancelled{ background:#fef2f2;color:#991b1b;border:1px solid #fecaca; }
    .ship-badge {
        display: inline-flex; align-items: center; gap: .25rem;
        padding: .25rem .7rem; border-radius: 50px;
        font-size: .72rem; font-weight: 700;
    }
    .ship-badge.delivered { background:#f0fdf4;color:#166534;border:1px solid #bbf7d0; }
    .ship-badge.other     { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .ship-badge.cancelled { background:#fef2f2;color:#991b1b;border:1px solid #fecaca; }

    /* Detail button */
    .btn-detail {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .4rem .9rem; border-radius: 8px;
        font-size: .8rem; font-weight: 600;
        background: #eff6ff; color: #2563eb;
        border: 1px solid #bfdbfe;
        text-decoration: none; transition: all .2s;
    }
    .btn-detail:hover { background: #2563eb; color: #fff; border-color: #2563eb; }

    /* Order ID */
    .order-id-chip {
        display: inline-flex; align-items: center;
        background: #f8faff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: .25rem .65rem;
        font-size: .82rem; font-weight: 800;
        color: #2563eb; letter-spacing: .5px;
    }
</style>

<div class="mb-4 d-flex align-items-center justify-content-between">
    <div>
        <p class="text-muted mb-1" style="font-size:.72rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">
            <i class="fa-solid fa-box-open text-primary me-1"></i> Admin
        </p>
        <h4 class="fw-800 text-dark mb-0" style="font-weight:800;letter-spacing:-.3px;">Quản Lý Đơn Hàng</h4>
    </div>
</div>

{{-- TABS --}}
<div class="admin-tabs">
    @foreach($tabs as $key => $tab)
        <a class="admin-tab {{ $activeTab === $key ? 'active' : '' }}"
           href="{{ route('admin.orders.index', array_merge(request()->query(), ['tab' => $key])) }}">
            {{ $tab['label'] }}
            <span class="tab-count {{ $activeTab === $key ? '' : '' }}"
                  style="background: {{ $activeTab === $key ? '#eff6ff' : '#f1f5f9' }};
                         color: {{ $activeTab === $key ? '#2563eb' : '#64748b' }};">
                {{ $tab['count'] }}
            </span>
        </a>
    @endforeach
</div>

<div class="admin-card shadow-sm">
    <div class="table-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã Đơn</th>
                    <th>Ngày Tạo</th>
                    <th style="width:22%;">Sản Phẩm</th>
                    <th class="text-end">Tổng Tiền</th>
                    <th>Khách Hàng</th>
                    <th class="text-center">Thanh Toán</th>
                    <th class="text-center">Vận Chuyển</th>
                    <th class="text-center" style="width:90px;">Chi Tiết</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <span class="order-id-chip">#DH{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td class="text-muted" style="font-size:.8rem;">
                            <i class="fa-regular fa-calendar fa-xs me-1"></i>
                            {{ $order->created_at->format('d/m/Y') }}<br>
                            <span style="font-size:.72rem;color:#94a3b8;">{{ $order->created_at->format('H:i') }}</span>
                        </td>
                        <td>
                            @foreach($order->items as $item)
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <div class="text-dark fw-600 text-truncate" style="font-size:.82rem;font-weight:600;max-width:160px;">
                                        {{ $item->product->name ?? 'SP Đã xóa' }}
                                    </div>
                                    <span class="flex-shrink-0" style="font-size:.72rem;color:#ef4444;font-weight:700;">×{{ $item->quantity }}</span>
                                </div>
                            @endforeach
                        </td>
                        <td class="text-end">
                            <span class="fw-800 text-danger" style="font-weight:800;font-size:.92rem;letter-spacing:-.3px;">
                                {{ number_format($order->total_price, 0, ',', '.') }} đ
                            </span>
                        </td>
                        <td>
                            <div class="fw-700 text-dark" style="font-weight:700;font-size:.88rem;">{{ $order->name ?? 'Khách lẻ' }}</div>
                            <div class="text-muted" style="font-size:.78rem;">
                                <i class="fa-solid fa-phone fa-xs me-1"></i>{{ $order->phone }}
                            </div>
                        </td>
                        <td class="text-center">
                            @php
                                $payStatus = $order->payment_status;
                                $payClass = $payStatus === 'paid' ? 'paid' : ($payStatus === 'cancelled' ? 'cancelled' : 'pending');
                            @endphp
                            <span class="pay-badge {{ $payClass }}">
                                <i class="fa-solid fa-circle fa-xs"></i>
                                {{ $paymentLabels[$payStatus] ?? $payStatus }}
                            </span>
                        </td>
                        <td class="text-center">
                            @php
                                $shipStatus = $order->shipping_status;
                                $shipClass = $shipStatus === 'delivered' ? 'delivered' : ($shipStatus === 'cancelled' ? 'cancelled' : 'other');
                            @endphp
                            <span class="ship-badge {{ $shipClass }}">
                                <i class="fa-solid fa-circle fa-xs"></i>
                                {{ $shippingLabels[$shipStatus] ?? $shipStatus }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-detail">
                                <i class="fa-solid fa-eye fa-xs"></i> Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="fa-solid fa-inbox fa-2x text-muted opacity-30 mb-2 d-block"></i>
                            <span class="text-muted">Không tìm thấy đơn hàng nào trong mục này.</span>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($orders->hasPages())
        <div class="px-4 py-3 border-top" style="border-color:#f1f5f9 !important;">
            {{ $orders->links() }}
        </div>
    @endif
</div>

@endsection