@extends('user.layout')
@section('title', 'Thanh Toán — WashingStore')

@section('content')

<style>
    :root {
        --primary: #2563eb;
        --border: #e2e8f0;
    }

    /* ── STEP LABEL ── */
    .step-label {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #f1f5f9;
    }
    .step-num {
        width: 34px; height: 34px;
        background: var(--primary);
        color: #fff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: .9rem;
        flex-shrink: 0;
    }
    .step-num.success { background: #22c55e; }
    .step-text { font-size: 1.05rem; font-weight: 700; color: #0f172a; }

    /* ── FORM INPUTS ── */
    .form-label-custom {
        font-size: .75rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: .4rem;
        display: block;
    }
    .form-control-custom, .form-select-custom {
        width: 100%;
        border: 2px solid var(--border);
        border-radius: 14px;
        padding: .75rem 1rem;
        font-size: .95rem;
        font-weight: 500;
        color: #0f172a;
        background: #fff;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
        appearance: none;
    }
    .form-control-custom:focus, .form-select-custom:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37,99,235,.1);
    }
    .form-control-custom::placeholder { color: #94a3b8; }
    .is-invalid { border-color: #ef4444 !important; }
    .invalid-feedback-custom { color: #ef4444; font-size: .8rem; margin-top: .3rem; font-weight: 500; }

    /* ── PAYMENT RADIO CARDS ── */
    .payment-radio { display: none; }
    .payment-card {
        border: 2px solid var(--border);
        background: #f8faff;
        border-radius: 18px;
        padding: 1.1rem 1.25rem;
        cursor: pointer;
        transition: all .22s;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    .payment-card:hover {
        background: #fff;
        border-color: #93c5fd;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(37,99,235,.08);
    }
    .payment-radio:checked + .payment-card {
        border-color: var(--primary);
        background: #eff6ff;
        box-shadow: 0 4px 20px rgba(37,99,235,.12);
    }
    .payment-radio:checked + .payment-card::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 0; right: 0;
        background: var(--primary);
        color: #fff;
        font-size: 10px;
        padding: 5px 10px;
        border-bottom-left-radius: 12px;
    }
    .payment-icon-box {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 2px 6px rgba(0,0,0,.06);
    }

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
    }
    .summary-header h6 { font-weight: 700; margin: 0; }
    .summary-row {
        display: flex; justify-content: space-between;
        padding: .65rem 0;
        font-size: .9rem;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
    }
    .summary-row:last-of-type { border-bottom: none; }

    /* ── PLACE ORDER BTN ── */
    .btn-place-order {
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
    .btn-place-order:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(34,197,94,.35); }
    .btn-place-order:disabled { background: #d1d5db; box-shadow: none; cursor: not-allowed; transform: none; }

    /* ── SELECT WRAPPER ── */
    .select-wrapper {
        position: relative;
    }
    .select-wrapper::after {
        content: '\f078';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: .7rem;
        color: #94a3b8;
        pointer-events: none;
    }
</style>

<div class="container py-5">
    {{-- Header --}}
    <div class="mb-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb" style="font-size:.85rem;">
                <li class="breadcrumb-item">
                    <a href="{{ route('cart.index') }}" class="text-decoration-none text-muted">
                        <i class="fa-solid fa-cart-shopping fa-xs me-1"></i>Giỏ hàng
                    </a>
                </li>
                <li class="breadcrumb-item active text-dark fw-500" style="font-weight:500;">Thanh toán</li>
            </ol>
        </nav>
        <h1 class="fw-800 text-dark mb-1" style="font-size:1.85rem;font-weight:800;letter-spacing:-.5px;">
            <i class="fa-solid fa-truck-fast text-primary me-2"></i>Giao Hàng &amp; Thanh Toán
        </h1>
        <p class="text-muted mb-0">Điền thông tin giao hàng để chúng tôi phục vụ bạn nhanh nhất.</p>
    </div>

    @php
        $cart = session('cart', []);
        $subtotal = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);
        $coupon = session('coupon');
        $discountAmount = $coupon ? $coupon['discount_amount'] : 0;
        $initialTotal = $subtotal - $discountAmount;
        if ($initialTotal < 0) $initialTotal = 0;
    @endphp

    <div class="row g-5">
        {{-- LEFT: Form --}}
        <div class="col-lg-8">
            <div class="bg-white border rounded-4 p-4 p-md-5" style="border-color:#e2e8f0 !important;">
                <form action="{{ route('checkout.index') }}" method="POST" id="checkoutForm">
                    @csrf
                    <input type="hidden" id="total_price_input" name="total_price" value="{{ $initialTotal }}">

                    {{-- STEP 1: Người nhận --}}
                    <div class="step-label">
                        <div class="step-num">1</div>
                        <div class="step-text">Thông Tin Người Nhận</div>
                    </div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label-custom">Họ và tên *</label>
                            <input type="text" name="name" class="form-control-custom"
                                   value="{{ Auth::user()->name }}" required placeholder="Tên người nhận hàng">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-custom">Số điện thoại *</label>
                            <input type="text" name="phone" class="form-control-custom"
                                   required placeholder="VD: 0909 123 456">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-custom">Tỉnh / Thành phố *</label>
                            <div class="select-wrapper">
                                <select id="province_select" class="form-select-custom" required>
                                    <option value="">-- Đang tải... --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">Quận / Huyện *</label>
                            <div class="select-wrapper">
                                <select id="district_select" name="to_district_id" class="form-select-custom" required disabled>
                                    <option value="">-- Chọn Quận/Huyện --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-custom">Phường / Xã *</label>
                            <div class="select-wrapper">
                                <select id="ward_select" name="to_ward_code" class="form-select-custom" required disabled>
                                    <option value="">-- Chọn Phường/Xã --</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label-custom">Địa chỉ chi tiết *</label>
                            <input type="text" name="address" class="form-control-custom"
                                   required placeholder="Số nhà, Tên đường, Tòa nhà...">
                        </div>
                    </div>

                    {{-- STEP 2: Phương thức thanh toán --}}
                    <div class="step-label">
                        <div class="step-num">2</div>
                        <div class="step-text">Phương Thức Thanh Toán</div>
                    </div>

                    <div class="row g-3 mb-5">
                        {{-- COD --}}
                        <div class="col-md-6">
                            <label class="w-100 position-relative">
                                <input type="radio" name="payment_method" id="pay_cod" value="cod" class="payment-radio" checked>
                                <div class="payment-card">
                                    <div class="payment-icon-box">
                                        <i class="fa-solid fa-money-bill-wave text-success fa-lg"></i>
                                    </div>
                                    <div>
                                        <div class="fw-700 text-dark mb-1" style="font-weight:700;font-size:.95rem;">Tiền mặt (COD)</div>
                                        <div class="text-muted" style="font-size:.8rem;">Thanh toán khi nhận hàng</div>
                                    </div>
                                </div>
                            </label>
                        </div>

                        {{-- MoMo --}}
                        <div class="col-md-6">
                            <label class="w-100 position-relative">
                                <input type="radio" name="payment_method" id="pay_momo" value="momo" class="payment-radio">
                                <div class="payment-card">
                                    <div class="payment-icon-box">
                                        <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" alt="MoMo" style="width:36px;object-fit:contain;">
                                    </div>
                                    <div>
                                        <div class="fw-700 text-dark mb-1" style="font-weight:700;font-size:.95rem;">Ví MoMo</div>
                                        <div class="text-muted" style="font-size:.8rem;">Quét mã QR tự động</div>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Submit (mobile) --}}
                    <div class="d-lg-none">
                        <button type="submit" class="btn-place-order">
                            <i class="fa-solid fa-check me-2"></i>Xác Nhận Đặt Hàng
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT: Summary Sticky --}}
        <div class="col-lg-4">
            <div class="summary-card">
                <div class="summary-header">
                    <h6><i class="fa-solid fa-receipt me-2"></i>Chi Tiết Thanh Toán</h6>
                </div>
                <div class="p-4">
                    {{-- Cart Items mini --}}
                    @foreach($cart as $id => $item)
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="flex-shrink-0 rounded-2 overflow-hidden border" style="width:42px;height:42px;background:#f8faff;">
                                @if(isset($item['image']) && $item['image'])
                                    <img src="{{ asset('storage/' . $item['image']) }}" style="width:42px;height:42px;object-fit:contain;padding:4px;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <i class="fa-solid fa-washing-machine text-muted fa-xs"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <div class="text-dark fw-600" style="font-size:.82rem;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $item['name'] }}</div>
                                <div class="text-muted" style="font-size:.75rem;">x{{ $item['quantity'] }}</div>
                            </div>
                            <div class="fw-700 text-dark flex-shrink-0" style="font-size:.85rem;font-weight:700;">
                                {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }} đ
                            </div>
                        </div>
                    @endforeach

                    <hr style="border-color:#f1f5f9;margin:1rem 0;">

                    <div class="summary-row">
                        <span>Tổng tiền hàng</span>
                        <strong class="text-dark">{{ number_format($subtotal, 0, ',', '.') }} đ</strong>
                    </div>
                    @if($coupon)
                    <div class="summary-row" style="color:#16a34a;">
                        <span><i class="fa-solid fa-tag me-1"></i>Giảm (<span class="fw-700 text-uppercase">{{ $coupon['code'] }}</span>)</span>
                        <strong>-{{ number_format($discountAmount, 0, ',', '.') }} đ</strong>
                    </div>
                    @endif
                    <div class="summary-row">
                        <span>Phí vận chuyển (GHN)</span>
                        <strong id="shipping_fee_text" class="text-primary">Đang tính...</strong>
                    </div>

                    {{-- Total --}}
                    <div class="mt-4 p-3 rounded-3" style="background:#f8faff;border:1.5px solid #e2e8f0;">
                        <div class="d-flex justify-content-between align-items-baseline">
                            <span class="fw-700 text-dark" style="font-weight:700;text-transform:uppercase;font-size:.85rem;letter-spacing:.5px;">Tổng cộng</span>
                            <span id="final_total_text" class="text-danger fw-800" style="font-size:1.5rem;font-weight:800;letter-spacing:-.5px;">{{ number_format($initialTotal, 0, ',', '.') }} đ</span>
                        </div>
                        <div class="text-muted mt-1" style="font-size:.72rem;">(Đã bao gồm VAT nếu có)</div>
                    </div>

                    <div class="mt-4 d-none d-lg-block">
                        <button type="submit" form="checkoutForm" class="btn-place-order">
                            <i class="fa-solid fa-check me-2"></i>Xác Nhận Đặt Hàng
                        </button>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('cart.index') }}" class="text-decoration-none text-muted fw-500" style="font-size:.85rem;font-weight:500;">
                            <i class="fa-solid fa-arrow-left fa-xs me-1"></i>Trở về Giỏ Hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const provinceSelect = document.getElementById('province_select');
        const districtSelect = document.getElementById('district_select');
        const wardSelect     = document.getElementById('ward_select');
        const shippingFeeText = document.getElementById('shipping_fee_text');
        const finalTotalText  = document.getElementById('final_total_text');
        const totalPriceInput = document.getElementById('total_price_input');

        const districtsUrl = "{{ route('locations.districts', ['provinceId' => '__PROVINCE__']) }}";
        const wardsUrl     = "{{ route('locations.wards', ['districtId' => '__DISTRICT__']) }}";
        const subtotal     = parseInt(totalPriceInput ? totalPriceInput.value : 0) || 0;
        const discountAmount = {{ $discountAmount }};

        fetch("{{ route('locations.provinces') }}")
            .then(res => res.json())
            .then(res => {
                if (res.data) {
                    let options = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                    res.data.forEach(p => { options += `<option value="${p.ProvinceID}">${p.ProvinceName}</option>`; });
                    provinceSelect.innerHTML = options;
                } else { provinceSelect.innerHTML = '<option value="">-- Lỗi tải dữ liệu --</option>'; }
            });

        provinceSelect.addEventListener('change', function () {
            districtSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
            districtSelect.disabled = true;
            wardSelect.innerHTML    = '<option value="">-- Chọn Phường/Xã --</option>';
            wardSelect.disabled     = true;
            updateTotals(0);
            if (!this.value) return;
            fetch(districtsUrl.replace('__PROVINCE__', this.value))
                .then(res => res.json())
                .then(res => {
                    if (res.data) {
                        let options = '<option value="">-- Chọn Quận/Huyện --</option>';
                        res.data.forEach(d => { options += `<option value="${d.DistrictID}">${d.DistrictName}</option>`; });
                        districtSelect.innerHTML = options;
                        districtSelect.disabled  = false;
                    }
                });
        });

        districtSelect.addEventListener('change', function () {
            wardSelect.innerHTML = '<option value="">-- Đang tải... --</option>';
            wardSelect.disabled  = true;
            updateTotals(0);
            if (!this.value) return;
            fetch(wardsUrl.replace('__DISTRICT__', this.value))
                .then(res => res.json())
                .then(res => {
                    if (res.data) {
                        let options = '<option value="">-- Chọn Phường/Xã --</option>';
                        res.data.forEach(w => { options += `<option value="${w.WardCode}">${w.WardName}</option>`; });
                        wardSelect.innerHTML = options;
                        wardSelect.disabled  = false;
                    }
                });
        });

        wardSelect.addEventListener('change', function () {
            if (!this.value || !districtSelect.value) return;
            shippingFeeText.innerHTML = '<span class="spinner-border spinner-border-sm text-primary"></span>';
            fetch("{{ route('locations.fee') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ to_district_id: districtSelect.value, to_ward_code: this.value })
            }).then(res => res.json()).then(res => {
                if (res.code === 200 && res.data) { updateTotals(parseInt(res.data.total) || 0); }
                else { shippingFeeText.innerText = 'Chưa hỗ trợ'; updateTotals(0); }
            });
        });

        function updateTotals(fee) {
            shippingFeeText.innerText = fee === 0 ? '0 đ' : new Intl.NumberFormat('vi-VN').format(fee) + ' đ';
            let finalAmount = subtotal + fee - discountAmount;
            if (finalAmount < 0) finalAmount = 0;
            finalTotalText.innerText = new Intl.NumberFormat('vi-VN').format(finalAmount) + ' đ';
            if (totalPriceInput) totalPriceInput.value = finalAmount;
        }

        // Submit Form
        const checkoutForm = document.getElementById('checkoutForm');
        checkoutForm.addEventListener('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
            const submitBtns = document.querySelectorAll('button[type="submit"][form="checkoutForm"], #checkoutForm button[type="submit"]');
            submitBtns.forEach(btn => {
                btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang Xử Lý...';
                btn.disabled  = true;
            });
            fetch("{{ route('checkout.index') }}", {
                method: 'POST', body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(r => r.json()).then(data => {
                if (data.status === 'success') {
                    if (data.is_momo || data.is_redirect) {
                        Swal.fire({ title: 'Chuyển Hướng Thanh Toán', text: data.message, icon: 'info', showConfirmButton: false, timer: 2000 })
                            .then(() => window.location.href = data.redirect_url);
                    } else {
                        Swal.fire({ title: 'Đặt Hàng Thành Công!', text: data.message, icon: 'success', confirmButtonText: 'Xem Đơn Hàng', confirmButtonColor: '#22c55e', allowOutsideClick: false })
                            .then(r => { if (r.isConfirmed) window.location.href = data.redirect_url; });
                    }
                } else {
                    Swal.fire('Có Lỗi Xảy Ra!', data.message, 'error');
                    submitBtns.forEach(btn => { btn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Xác Nhận Đặt Hàng'; btn.disabled = false; });
                }
            }).catch(() => {
                Swal.fire('Lỗi!', 'Có lỗi kết nối máy chủ.', 'error');
                submitBtns.forEach(btn => { btn.innerHTML = '<i class="fa-solid fa-check me-2"></i>Xác Nhận Đặt Hàng'; btn.disabled = false; });
            });
        });
    });
</script>

@endsection