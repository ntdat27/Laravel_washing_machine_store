@extends('user.layout')
@section('title', 'Giỏ hàng — WashingStore')

@section('content')

<style>
    :root {
        --primary: #2563eb;
        --danger:  #ef4444;
        --success: #22c55e;
        --border:  #e2e8f0;
        --surface: #f8faff;
    }

    /* ── CART ITEM ROW ── */
    .cart-row {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid var(--border);
        transition: background .2s;
    }
    .cart-row:last-child { border-bottom: none; }
    .cart-row:hover { background: #f8faff; }

    /* ── PRODUCT THUMBNAIL ── */
    .cart-thumb {
        width: 88px; height: 88px;
        border-radius: 14px;
        object-fit: contain;
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        border: 1px solid var(--border);
        padding: 6px;
        flex-shrink: 0;
    }
    .cart-thumb-placeholder {
        width: 88px; height: 88px;
        border-radius: 14px;
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── QTY STEPPER ── */
    .qty-group {
        display: inline-flex;
        align-items: center;
        border: 1.5px solid var(--border);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
    }
    .qty-group input[type="number"] {
        width: 52px;
        border: none;
        text-align: center;
        font-weight: 700;
        font-size: .95rem;
        padding: .5rem 0;
        outline: none;
        background: transparent;
        -moz-appearance: textfield;
    }
    .qty-group input[type="number"]::-webkit-inner-spin-button,
    .qty-group input[type="number"]::-webkit-outer-spin-button { appearance: none; }
    .qty-submit-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: .55rem .9rem;
        font-size: .8rem;
        cursor: pointer;
        transition: background .2s;
        display: flex; align-items: center;
    }
    .qty-submit-btn:hover { background: #1d4ed8; }

    /* ── DELETE BTN ── */
    .btn-delete {
        width: 34px; height: 34px;
        border-radius: 50%;
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: var(--danger);
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all .2s;
        flex-shrink: 0;
    }
    .btn-delete:hover { background: var(--danger); color: #fff; transform: scale(1.1); }

    /* ── SUMMARY BOX ── */
    .summary-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        position: sticky;
        top: 90px;
    }
    .summary-header {
        background: linear-gradient(135deg,#2563eb,#1d4ed8);
        padding: 1.25rem 1.5rem;
        color: #fff;
        display: flex; align-items: center; gap: .6rem;
    }
    .summary-header h6 { font-weight: 700; font-size: 1rem; margin: 0; }
    .summary-row {
        display: flex; justify-content: space-between;
        padding: .6rem 0;
        font-size: .9rem;
        color: #475569;
        border-bottom: 1px solid #f1f5f9;
    }
    .summary-row:last-of-type { border-bottom: none; }
    .summary-total {
        display: flex; justify-content: space-between; align-items: center;
        padding: 1rem 1.5rem;
        background: #f8faff;
        border-top: 2px solid var(--border);
        margin: 0 -1.5rem;
    }

    /* ── COUPON INPUT ── */
    .coupon-input-group {
        display: flex;
        border: 2px solid var(--border);
        border-radius: 14px;
        overflow: hidden;
        transition: border-color .2s;
    }
    .coupon-input-group:focus-within { border-color: var(--primary); }
    .coupon-input-group input {
        border: none; outline: none;
        padding: .65rem 1rem;
        flex: 1;
        font-size: .9rem;
        background: transparent;
    }
    .coupon-input-group .apply-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: .65rem 1.1rem;
        font-weight: 600;
        font-size: .85rem;
        cursor: pointer;
        transition: background .2s;
    }
    .coupon-input-group .apply-btn:hover { background: #1d4ed8; }

    /* ── APPLIED COUPON ── */
    .coupon-applied {
        display: flex; align-items: center; gap: .6rem;
        background: #f0fdf4;
        border: 1.5px dashed #86efac;
        border-radius: 12px;
        padding: .6rem 1rem;
        font-size: .85rem;
    }
    .coupon-applied strong { color: #16a34a; text-transform: uppercase; }

    /* ── CHECKOUT BTN ── */
    .btn-checkout {
        background: linear-gradient(135deg,#22c55e,#16a34a);
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: 1rem;
        font-size: 1rem;
        font-weight: 700;
        width: 100%;
        cursor: pointer;
        transition: all .3s;
        letter-spacing: .3px;
        box-shadow: 0 4px 16px rgba(34,197,94,.25);
    }
    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(34,197,94,.35);
    }
    .btn-checkout:disabled {
        background: #d1d5db;
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }
</style>

<div class="container py-5">
    {{-- Header --}}
    <div class="mb-5">
        <p class="text-muted mb-1" style="font-size:.8rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">
            <i class="fa-solid fa-cart-shopping text-primary me-1"></i> Mua sắm
        </p>
        <h1 class="fw-800 text-dark mb-1" style="font-size:1.85rem;font-weight:800;letter-spacing:-.5px;">Giỏ hàng của bạn</h1>
        <p class="text-muted mb-0">Kiểm tra lại sản phẩm trước khi thanh toán.</p>
    </div>

    @if(!empty($cart) && count($cart) > 0)
        <form id="checkout-form" action="{{ route('checkout.index') }}" method="GET"></form>

        <div class="row g-4">
            {{-- LEFT: Cart Items --}}
            <div class="col-lg-8">
                <div class="bg-white border rounded-4 overflow-hidden" style="border-color:#e2e8f0 !important;">
                    {{-- Table head --}}
                    <div class="d-flex align-items-center px-4 py-3 border-bottom" style="background:#f8faff;border-color:#e2e8f0 !important;">
                        <div class="d-flex align-items-center gap-2 me-3">
                            <input class="form-check-input p-2 shadow-none" type="checkbox" id="check-all" checked
                                   style="width:18px;height:18px;border:2px solid #cbd5e1;border-radius:5px;cursor:pointer;">
                            <label for="check-all" class="text-muted fw-600" style="font-size:.82rem;font-weight:600;cursor:pointer;text-transform:uppercase;letter-spacing:.5px;">
                                Chọn tất cả
                            </label>
                        </div>
                        <div class="ms-auto text-muted" style="font-size:.82rem;">
                            <i class="fa-solid fa-cube fa-xs me-1"></i> {{ count($cart) }} sản phẩm
                        </div>
                    </div>

                    {{-- Items --}}
                    @php $total = 0; @endphp
                    @foreach($cart as $id => $details)
                        @php
                            $thanh_tien = $details['price'] * $details['quantity'];
                            $total += $thanh_tien;
                        @endphp
                        <div class="cart-row">
                            {{-- Checkbox --}}
                            <div class="flex-shrink-0">
                                <input class="form-check-input item-check shadow-none" type="checkbox"
                                       name="selected_items[{{ $id }}]" value="{{ $details['quantity'] }}"
                                       data-price="{{ $thanh_tien }}" form="checkout-form" checked
                                       style="width:18px;height:18px;border:2px solid #cbd5e1;border-radius:5px;cursor:pointer;">
                            </div>

                            {{-- Thumbnail --}}
                            @if(isset($details['image']) && $details['image'])
                                <img src="{{ asset('storage/' . $details['image']) }}" class="cart-thumb" alt="{{ $details['name'] }}">
                            @else
                                <div class="cart-thumb-placeholder">
                                    <i class="fa-solid fa-washing-machine text-muted fa-lg opacity-40"></i>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-grow-1 min-width-0">
                                <h6 class="fw-700 text-dark mb-1" style="font-weight:700;font-size:.95rem;">{{ $details['name'] }}</h6>
                                @if(isset($details['color_name']) && $details['color_name'])
                                    <span class="badge mb-1" style="font-size:.72rem;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;">
                                        {{ $details['color_name'] }}
                                    </span>
                                @endif
                                <div class="text-danger fw-700 mt-1" style="font-size:.9rem;font-weight:700;">
                                    {{ number_format($details['price'], 0, ',', '.') }} đ
                                </div>
                            </div>

                            {{-- Qty --}}
                            <form action="{{ route('cart.update', $id) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                @method('PATCH')
                                <div class="qty-group">
                                    <input type="number" name="quantity" value="{{ $details['quantity'] }}" min="1">
                                    <button type="submit" class="qty-submit-btn">
                                        <i class="fa-solid fa-rotate-right fa-xs"></i>
                                    </button>
                                </div>
                            </form>

                            {{-- Total price --}}
                            <div class="text-end flex-shrink-0" style="min-width:90px;">
                                <div class="fw-800 text-dark item-total-price" style="font-size:1rem;font-weight:800;">
                                    {{ number_format($thanh_tien, 0, ',', '.') }} đ
                                </div>
                                <div class="text-muted" style="font-size:.75rem;">x{{ $details['quantity'] }}</div>
                            </div>

                            {{-- Delete --}}
                            <form action="{{ route('cart.remove', $id) }}" method="POST" class="flex-shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" title="Xóa sản phẩm">
                                    <i class="fa-solid fa-trash-can fa-xs"></i>
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <a href="{{ route('welcome') }}"
                       class="d-inline-flex align-items-center gap-2 text-decoration-none fw-600"
                       style="color:#2563eb;font-weight:600;font-size:.9rem;">
                        <i class="fa-solid fa-arrow-left fa-sm"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>

            {{-- RIGHT: Summary Sticky --}}
            <div class="col-lg-4">
                <div class="summary-card">
                    <div class="summary-header">
                        <i class="fa-solid fa-receipt"></i>
                        <h6>Tóm tắt đơn hàng</h6>
                    </div>
                    <div class="p-4">
                        {{-- Coupon --}}
                        <div class="mb-4">
                            <p class="fw-700 text-dark mb-2" style="font-size:.82rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Mã giảm giá</p>
                            @php $coupon = session('coupon'); @endphp
                            @if($coupon)
                                <div class="coupon-applied mb-2">
                                    <i class="fa-solid fa-ticket text-success"></i>
                                    <strong>{{ $coupon['code'] }}</strong>
                                    <span class="text-success ms-1" style="font-size:.78rem;">– Đã áp dụng</span>
                                    <button type="button" class="btn-delete ms-auto" id="btn-remove-coupon" title="Gỡ coupon" style="width:26px;height:26px;flex-shrink:0;">
                                        <i class="fa-solid fa-xmark" style="font-size:.7rem;"></i>
                                    </button>
                                </div>
                                @php $total -= $coupon['discount_amount']; @endphp
                            @else
                                <div class="coupon-input-group">
                                    <input type="text" id="coupon-code" placeholder="Nhập mã voucher...">
                                    <button class="apply-btn" type="button" id="btn-apply-coupon">Áp dụng</button>
                                </div>
                            @endif
                        </div>

                        <hr style="border-color:#f1f5f9;margin:.5rem 0 1rem;">

                        {{-- Rows --}}
                        <div class="summary-row">
                            <span>Tạm tính</span>
                            <span class="fw-600 text-dark" id="subtotal-price" data-value="{{ $total }}" style="font-weight:600;">
                                {{ number_format($total, 0, ',', '.') }} đ
                            </span>
                        </div>
                        @if($coupon)
                        <div class="summary-row" style="color:#16a34a;">
                            <span><i class="fa-solid fa-tag me-1"></i>Giảm giá</span>
                            <span class="fw-600" style="font-weight:600;">-{{ number_format($coupon['discount_amount'], 0, ',', '.') }} đ</span>
                        </div>
                        @endif
                        <div class="summary-row">
                            <span>Phí vận chuyển</span>
                            <span class="text-muted" style="font-size:.82rem;">Tính khi thanh toán</span>
                        </div>

                        {{-- Total --}}
                        <div style="margin: 1rem -1rem -1rem; padding: 1rem 1.5rem; background:#f8faff; border-top: 2px solid #e2e8f0; border-radius: 0 0 16px 16px;">
                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                <span class="fw-700 text-dark" style="font-weight:700;">Tổng tiền</span>
                                <span class="fw-800 text-danger" id="total-price" style="font-size:1.5rem;font-weight:800;letter-spacing:-.5px;">
                                    {{ number_format($total > 0 ? $total : 0, 0, ',', '.') }} đ
                                </span>
                            </div>
                            <p class="text-muted mb-3" style="font-size:.75rem;">*Chưa bao gồm phí vận chuyển</p>
                            <button type="submit" form="checkout-form" class="btn-checkout" id="btn-checkout">
                                Tiến Hành Thanh Toán <i class="fa-solid fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Trust icons --}}
                <div class="d-flex justify-content-around mt-3 text-center">
                    <div>
                        <i class="fa-solid fa-shield-halved text-primary mb-1 d-block"></i>
                        <span style="font-size:.72rem;color:#64748b;font-weight:500;">Bảo mật</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-rotate-left text-primary mb-1 d-block"></i>
                        <span style="font-size:.72rem;color:#64748b;font-weight:500;">Đổi trả 7 ngày</span>
                    </div>
                    <div>
                        <i class="fa-solid fa-award text-primary mb-1 d-block"></i>
                        <span style="font-size:.72rem;color:#64748b;font-weight:500;">Hàng chính hãng</span>
                    </div>
                </div>
            </div>
        </div>

    @else
        <div class="text-center py-5">
            <div class="d-inline-flex align-items-center justify-content-center mb-4"
                 style="width:100px;height:100px;background:#eff6ff;border-radius:50%;">
                <i class="fa-solid fa-cart-shopping fa-2x text-primary"></i>
            </div>
            <h4 class="fw-700 text-dark mb-2" style="font-weight:700;">Giỏ hàng của bạn đang trống!</h4>
            <p class="text-muted mb-4">Hãy tìm cho mình chiếc máy giặt ưng ý nhé.</p>
            <a href="{{ route('welcome') }}"
               class="btn rounded-pill px-5 py-2 fw-600 shadow-sm"
               style="background:#2563eb;color:#fff;font-weight:600;">
                Khám Phá Cửa Hàng <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const checkAll      = document.getElementById('check-all');
        const itemChecks    = document.querySelectorAll('.item-check');
        const subtotalEl    = document.getElementById('subtotal-price');
        const totalPriceEl  = document.getElementById('total-price');
        const btnCheckout   = document.getElementById('btn-checkout');

        function formatVND(n) { return new Intl.NumberFormat('vi-VN').format(n) + ' đ'; }

        function calculateTotal() {
            let total = 0, checkedCount = 0;
            itemChecks.forEach(c => {
                if (c.checked) { total += parseFloat(c.getAttribute('data-price')); checkedCount++; }
            });
            if (subtotalEl) subtotalEl.innerText = formatVND(total);
            if (totalPriceEl) totalPriceEl.innerText = formatVND(total);
            if (btnCheckout) btnCheckout.disabled = checkedCount === 0;
        }

        if (checkAll) {
            checkAll.addEventListener('change', function () {
                itemChecks.forEach(c => c.checked = checkAll.checked);
                calculateTotal();
            });
            itemChecks.forEach(c => {
                c.addEventListener('change', function () {
                    if (!this.checked) checkAll.checked = false;
                    if (document.querySelectorAll('.item-check:checked').length === itemChecks.length) checkAll.checked = true;
                    calculateTotal();
                });
            });
        }

        // Coupon Apply
        const btnApply  = document.getElementById('btn-apply-coupon');
        const btnRemove = document.getElementById('btn-remove-coupon');
        const couponInput = document.getElementById('coupon-code');

        if (btnApply) {
            btnApply.addEventListener('click', function () {
                const code = couponInput.value.trim();
                const rawSubtotal = parseFloat(subtotalEl?.getAttribute('data-value') || 0);
                if (!code) { Swal.fire('Lưu ý', 'Vui lòng nhập mã giảm giá', 'warning'); return; }
                btnApply.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
                btnApply.disabled = true;
                fetch('{{ route("cart.coupon.apply") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ code, subtotal: rawSubtotal })
                }).then(r => r.json()).then(data => {
                    if (data.status === 'success') { window.location.reload(); }
                    else { Swal.fire('Lỗi', data.message, 'error'); btnApply.innerHTML = 'Áp dụng'; btnApply.disabled = false; }
                }).catch(console.error);
            });
        }

        if (btnRemove) {
            btnRemove.addEventListener('click', function () {
                fetch('{{ route("cart.coupon.remove") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                }).then(r => r.json()).then(data => { if (data.status === 'success') window.location.reload(); }).catch(console.error);
            });
        }
    });
</script>

@endsection