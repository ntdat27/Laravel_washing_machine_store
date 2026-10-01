@extends('user.layout')
@section('title', 'Đơn Hàng Của Tôi — WashingStore')

@section('content')

<style>
    :root {
        --primary: #2563eb;
        --border: #e2e8f0;
        --surface: #f8faff;
    }

    /* ── STATUS BADGE PASTEL ── */
    .status-pill {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .4rem 1rem;
        border-radius: 50px;
        font-size: .78rem;
        font-weight: 700;
        letter-spacing: .2px;
    }
    .status-pill.pending   { background:#fefce8;color:#854d0e;border:1px solid #fde68a; }
    .status-pill.info      { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .status-pill.primary   { background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe; }
    .status-pill.success   { background:#f0fdf4;color:#166534;border:1px solid #bbf7d0; }
    .status-pill.danger    { background:#fef2f2;color:#991b1b;border:1px solid #fecaca; }
    .status-pill.secondary { background:#f8fafc;color:#475569;border:1px solid #e2e8f0; }

    /* ── ORDER CARD ── */
    .order-card {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: 20px;
        overflow: hidden;
        transition: all .25s;
    }
    .order-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(0,0,0,.08) !important;
        border-color: #bfdbfe;
    }
    .order-header {
        background: #f8faff;
        border-bottom: 1px solid var(--border);
        padding: 1rem 1.5rem;
        display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: .75rem;
    }
    .order-id-badge {
        display: inline-flex; align-items: center;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: .3rem .8rem;
        font-weight: 800;
        font-size: .88rem;
        color: var(--primary);
        letter-spacing: .5px;
    }

    /* ── PRODUCT ITEM ── */
    .order-item {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        transition: background .2s;
    }
    .order-item:last-child { border-bottom: none; }
    .order-item:hover { background: #fafbff; }
    .item-thumb {
        width: 80px; height: 80px;
        border-radius: 12px;
        object-fit: contain;
        border: 1px solid var(--border);
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        padding: 6px;
        flex-shrink: 0;
    }
    .item-thumb-placeholder {
        width: 80px; height: 80px;
        border-radius: 12px;
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        border: 1px solid var(--border);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── FOOTER ── */
    .order-footer {
        background: #f8faff;
        border-top: 1px solid var(--border);
        padding: 1rem 1.5rem;
    }

    /* ── ACTION BTNS ── */
    .btn-cancel-order {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1.25rem;
        border-radius: 50px;
        font-size: .85rem;
        font-weight: 600;
        border: 1.5px solid #fecaca;
        color: #dc2626;
        background: #fef2f2;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-cancel-order:hover { background: #dc2626; color: #fff; border-color: #dc2626; }
    .btn-buy-again {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .5rem 1.25rem;
        border-radius: 50px;
        font-size: .85rem;
        font-weight: 600;
        border: 1.5px solid #bfdbfe;
        color: var(--primary);
        background: #eff6ff;
        cursor: pointer;
        text-decoration: none;
        transition: all .2s;
    }
    .btn-buy-again:hover { background: var(--primary); color: #fff; border-color: var(--primary); }

    /* ── REVIEW BADGE ── */
    .review-done-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .35rem .85rem;
        border-radius: 50px;
        font-size: .75rem;
        font-weight: 600;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #16a34a;
    }
    .btn-review {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .45rem 1rem;
        border-radius: 50px;
        font-size: .8rem;
        font-weight: 700;
        background: #fef9c3;
        border: 1.5px solid #fde68a;
        color: #92400e;
        cursor: pointer;
        transition: all .2s;
    }
    .btn-review:hover { background: #f59e0b; color: #fff; border-color: #f59e0b; }

    /* ── TIMELINE (GHN code) ── */
    .ghn-badge {
        display: inline-flex; align-items: center; gap: .35rem;
        padding: .3rem .85rem;
        border-radius: 50px;
        font-size: .78rem;
        font-weight: 600;
        background: #f8fafc;
        border: 1px solid var(--border);
        color: #64748b;
    }
</style>

<div class="container py-5">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
        <div>
            <p class="text-muted mb-1" style="font-size:.8rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">
                <i class="fa-solid fa-box-open text-primary me-1"></i> Tài khoản
            </p>
            <h1 class="fw-800 text-dark mb-1" style="font-size:1.85rem;font-weight:800;letter-spacing:-.5px;">Lịch Sử Đơn Hàng</h1>
            <p class="text-muted mb-0">Theo dõi lịch sử và trạng thái giao hàng của bạn.</p>
        </div>
        <a href="{{ route('welcome') }}"
           class="btn rounded-pill fw-600 px-4"
           style="font-weight:600;background:#eff6ff;color:#2563eb;border:1.5px solid #bfdbfe;">
            <i class="fa-solid fa-cart-shopping me-2"></i>Tiếp tục mua sắm
        </a>
    </div>

    @if($orders->isEmpty())
        <div class="text-center py-5 bg-white border rounded-4" style="border-color:#e2e8f0 !important;">
            <div class="d-inline-flex align-items-center justify-content-center mb-4"
                 style="width:100px;height:100px;background:#f0f9ff;border-radius:50%;">
                <i class="fa-solid fa-box-open fa-2x" style="color:#0ea5e9;"></i>
            </div>
            <h4 class="fw-700 text-dark mb-2" style="font-weight:700;">Bạn chưa có đơn hàng nào</h4>
            <p class="text-muted mb-4">Hãy khám phá các sản phẩm máy giặt chính hãng nhé!</p>
            <a href="{{ route('welcome') }}" class="btn rounded-pill px-5 fw-600" style="background:#2563eb;color:#fff;font-weight:600;">
                Khám phá ngay <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>

    @else
        @php
            $statusMap = [
                'pending'       => ['Chờ xử lý',          'pending',   'fa-clock'],
                'not_shipped'   => ['Chưa giao hàng',      'secondary', 'fa-box'],
                'processing'    => ['Đang xử lý',          'info',      'fa-gear'],
                'ready_to_pick' => ['Chờ lấy hàng',        'info',      'fa-hand-point-right'],
                'picking'       => ['Đang lấy hàng',       'info',      'fa-person-walking'],
                'picked'        => ['Đã lấy hàng',         'info',      'fa-check-circle'],
                'storing'       => ['Đang lưu kho',        'secondary', 'fa-warehouse'],
                'transporting'  => ['Đang trung chuyển',   'info',      'fa-truck-moving'],
                'sorting'       => ['Đang phân loại',      'info',      'fa-layer-group'],
                'delivering'    => ['Đang giao hàng',      'primary',   'fa-truck-fast'],
                'delivered'     => ['Đã hoàn thành',       'success',   'fa-circle-check'],
                'return'        => ['Chờ hoàn hàng',       'danger',    'fa-rotate-left'],
                'returning'     => ['Đang hoàn hàng',      'danger',    'fa-rotate-left'],
                'returned'      => ['Đã hoàn hàng',        'danger',    'fa-reply-all'],
                'cancelled'     => ['Đã hủy',              'danger',    'fa-ban'],
            ];
            $cancellableStatuses = ['pending', 'not_shipped', 'processing'];
        @endphp

        <div class="d-flex flex-column gap-4">
            @foreach($orders as $order)
                @php
                    [$statusLabel, $statusColor, $statusIcon] = $statusMap[$order->shipping_status] ?? [$order->shipping_status, 'secondary', 'fa-circle'];
                    $canCancel = in_array($order->shipping_status, $cancellableStatuses);
                @endphp

                <div class="order-card">
                    {{-- Header --}}
                    <div class="order-header">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="order-id-badge">
                                <i class="fa-solid fa-hashtag fa-xs me-1"></i>DH{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}
                            </div>
                            <span class="text-muted" style="font-size:.82rem;font-weight:500;">
                                <i class="fa-regular fa-calendar-clock me-1 text-primary"></i>
                                {{ $order->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            @if($order->ghn_order_code)
                                <span class="ghn-badge">
                                    <i class="fa-solid fa-truck-fast text-primary"></i>
                                    GHN: {{ $order->ghn_order_code }}
                                </span>
                            @endif
                            <span class="status-pill {{ $statusColor }}">
                                <i class="fa-solid {{ $statusIcon }} fa-xs"></i>
                                {{ $statusLabel }}
                            </span>
                        </div>
                    </div>

                    {{-- Items --}}
                    @foreach($order->items as $item)
                        <div class="order-item">
                            {{-- Ảnh --}}
                            @php $img = $item->variant?->image ?? $item->product?->image ?? null; @endphp
                            @if($img)
                                <img src="{{ asset('storage/' . $img) }}" alt="{{ $item->product?->name }}" class="item-thumb">
                            @else
                                <div class="item-thumb-placeholder">
                                    <i class="fa-solid fa-washing-machine text-muted fa-lg opacity-40"></i>
                                </div>
                            @endif

                            {{-- Info --}}
                            <div class="flex-grow-1 min-width-0">
                                <a href="{{ route('user.products.show', $item->product_id) }}"
                                   class="fw-700 text-dark text-decoration-none d-block"
                                   style="font-size:.95rem;font-weight:700;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                    {{ $item->product?->name ?? 'Sản phẩm đã bị xóa' }}
                                </a>
                                @if($item->variant)
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="text-muted" style="font-size:.78rem;font-weight:500;">Phân loại:</span>
                                        @if($item->variant->color_code)
                                            <span class="rounded-circle border d-inline-block"
                                                  style="width:14px;height:14px;background:{{ $item->variant->color_code }};"></span>
                                        @endif
                                        <span class="badge px-2 py-1" style="font-size:.72rem;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;">{{ $item->variant->color_name }}</span>
                                    </div>
                                @endif
                                <div class="mt-1" style="font-size:.82rem;color:#64748b;">
                                    Số lượng: <strong class="text-dark">{{ $item->quantity }}</strong>
                                </div>
                            </div>

                            {{-- Price + Review --}}
                            <div class="text-end flex-shrink-0 d-flex flex-column align-items-end gap-2">
                                <div class="fw-800 text-danger" style="font-size:1rem;font-weight:800;">
                                    {{ number_format($item->price * $item->quantity, 0, ',', '.') }} đ
                                </div>
                                <div class="text-muted" style="font-size:.78rem;text-decoration:line-through;">
                                    {{ number_format($item->price, 0, ',', '.') }} đ/sp
                                </div>

                                @if($order->shipping_status === 'delivered')
                                    @php $reviewed = $item->review !== null; @endphp
                                    @if($reviewed)
                                        <span class="review-done-badge">
                                            <i class="fa-solid fa-star fa-xs"></i>Đã đánh giá {{ $item->review->rating }}/5
                                        </span>
                                    @else
                                        <button type="button"
                                                class="btn-review btn-open-review"
                                                data-order-id="{{ $order->id }}"
                                                data-item-id="{{ $item->id }}"
                                                data-product-name="{{ $item->product?->name }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reviewModal">
                                            <i class="fa-solid fa-star fa-xs"></i> Đánh Giá
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach

                    {{-- Footer --}}
                    <div class="order-footer">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="text-muted" style="font-size:.82rem;">
                                <div class="mb-1">
                                    <i class="fa-solid fa-location-dot text-primary me-1"></i>
                                    <strong class="text-dark">{{ $order->address }}</strong>
                                </div>
                                <div>
                                    <i class="fa-solid fa-phone text-primary me-1"></i>
                                    <strong class="text-dark">{{ $order->phone }}</strong>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                {{-- Total --}}
                                <div class="text-end">
                                    <div class="text-muted" style="font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.5px;">Thành tiền</div>
                                    <div class="fw-800 text-danger" style="font-size:1.35rem;font-weight:800;letter-spacing:-.5px;">
                                        {{ number_format($order->total_price, 0, ',', '.') }} đ
                                    </div>
                                </div>

                                {{-- Action --}}
                                @if($canCancel)
                                    <form action="{{ route('user.orders.cancel', $order->id) }}"
                                          method="POST"
                                          class="cancel-form m-0"
                                          data-order-id="{{ str_pad($order->id, 6, '0', STR_PAD_LEFT) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                                class="btn-cancel-order btn-cancel"
                                                data-form-id="{{ $order->id }}">
                                            <i class="fa-solid fa-xmark fa-xs"></i>Hủy Đơn
                                        </button>
                                    </form>
                                @elseif($order->shipping_status === 'delivered')
                                    <a href="{{ route('welcome') }}" class="btn-buy-again">
                                        <i class="fa-solid fa-bag-shopping fa-xs"></i>Mua lại
                                    </a>
                                @elseif($order->shipping_status === 'cancelled')
                                    <span class="status-pill danger"><i class="fa-solid fa-ban fa-xs"></i>Đã Hủy</span>
                                @else
                                    <span class="status-pill secondary"><i class="fa-solid fa-clock fa-xs"></i>Đang Xử Lý</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($orders->hasPages())
            <div class="d-flex justify-content-center mt-5">
                {{ $orders->onEachSide(1)->links('pagination::bootstrap-5') }}
            </div>
        @endif
    @endif
</div>

{{-- ── MODAL ĐÁNH GIÁ ── --}}
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius:20px;overflow:hidden;">
            <div class="modal-header border-0 px-4 pt-4 pb-2" style="background:linear-gradient(135deg,#fef9c3,#fef3c7);">
                <h5 class="modal-title fw-700 text-dark" id="reviewModalLabel" style="font-weight:700;">
                    <i class="fa-solid fa-star text-warning me-2"></i>Đánh Giá Sản Phẩm
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4 pb-3 border-bottom" style="border-color:#f1f5f9 !important;">
                    <p class="text-muted mb-1" style="font-size:.82rem;font-weight:600;">Đang đánh giá:</p>
                    <h6 class="fw-700 text-dark" id="review-product-name" style="font-weight:700;">--</h6>
                </div>

                <div class="text-center mb-4">
                    <label class="fw-700 text-dark mb-3 d-block" style="font-size:.82rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Mức độ hài lòng</label>
                    <div class="d-flex justify-content-center gap-2 mb-2">
                        @for($s = 1; $s <= 5; $s++)
                            <i class="fa-regular fa-star review-star"
                               style="font-size:2.2rem;cursor:pointer;color:#f59e0b;transition:transform .15s;"
                               data-value="{{ $s }}"
                               onmouseenter="hoverStars({{ $s }})"
                               onmouseleave="resetStars()"
                               onclick="selectStar({{ $s }})"></i>
                        @endfor
                    </div>
                    <span id="star-label" class="badge px-3 py-2" style="font-size:.8rem;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;border-radius:50px;">Chưa chọn</span>
                    <input type="hidden" id="review-rating" value="0">
                </div>

                <div class="mb-3">
                    <label class="fw-700 text-dark mb-2 d-block" style="font-size:.82rem;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Chia sẻ thêm</label>
                    <textarea id="review-comment"
                              class="w-100 shadow-none"
                              style="border:2px solid #e2e8f0;border-radius:14px;padding:.75rem 1rem;font-size:.9rem;outline:none;resize:none;transition:border-color .2s;"
                              rows="4"
                              placeholder="Hãy chia sẻ nhận xét của bạn..."
                              maxlength="1000"
                              onfocus="this.style.borderColor='#2563eb'" onblur="this.style.borderColor='#e2e8f0'"></textarea>
                    <div class="text-end mt-1">
                        <small id="comment-counter" class="text-muted" style="font-size:.75rem;">0 / 1000 ký tự</small>
                    </div>
                </div>

                <input type="hidden" id="review-order-id" value="">
                <input type="hidden" id="review-item-id" value="">
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-0 gap-2">
                <button type="button" class="btn rounded-pill px-4 fw-600 border shadow-sm" style="font-weight:600;" data-bs-dismiss="modal">Trở Về</button>
                <button type="button" class="btn rounded-pill px-5 fw-700 shadow-sm" id="btn-submit-review"
                        style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;font-weight:700;">
                    Hoàn Tất Gửi <i class="fa-solid fa-paper-plane ms-2 fa-sm"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var selectedStar = 0;
var starLabels   = ['', 'Rất tệ 😞', 'Không hài lòng 😕', 'Bình thường 😐', 'Hài lòng 😊', 'Tuyệt vời! 🌟'];
var starColors   = ['','#ef4444','#f97316','#eab308','#22c55e','#16a34a'];

function hoverStars(n) {
    document.querySelectorAll('.review-star').forEach((el, i) => {
        el.className = 'fa-' + (i < n ? 'solid' : 'regular') + ' fa-star review-star';
    });
}
function resetStars() {
    document.querySelectorAll('.review-star').forEach((el, i) => {
        el.className = 'fa-' + (i < selectedStar ? 'solid' : 'regular') + ' fa-star review-star';
    });
}
function selectStar(n) {
    selectedStar = n;
    document.getElementById('review-rating').value = n;
    const lbl = document.getElementById('star-label');
    lbl.textContent = n + ' Sao — ' + starLabels[n];
    lbl.style.cssText = `font-size:.8rem;background:${starColors[n]}22;color:${starColors[n]};border:1px solid ${starColors[n]}66;border-radius:50px;padding:.3rem 1rem;font-weight:700;`;
    resetStars();
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-open-review').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('review-product-name').textContent = this.getAttribute('data-product-name');
            document.getElementById('review-order-id').value = this.getAttribute('data-order-id');
            document.getElementById('review-item-id').value  = this.getAttribute('data-item-id');
            selectedStar = 0;
            document.getElementById('review-rating').value = '0';
            document.getElementById('review-comment').value = '';
            const lbl = document.getElementById('star-label');
            lbl.textContent = 'Chưa chọn';
            lbl.style.cssText = 'font-size:.8rem;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;border-radius:50px;padding:.3rem 1rem;font-weight:600;';
            document.getElementById('comment-counter').textContent = '0 / 1000 ký tự';
            resetStars();
        });
    });

    document.getElementById('review-comment')?.addEventListener('input', function () {
        document.getElementById('comment-counter').textContent = this.value.length + ' / 1000 ký tự';
    });

    document.getElementById('btn-submit-review')?.addEventListener('click', function () {
        const rating  = parseInt(document.getElementById('review-rating').value);
        const comment = document.getElementById('review-comment').value.trim();
        const orderId = document.getElementById('review-order-id').value;
        const itemId  = document.getElementById('review-item-id').value;

        if (rating < 1 || rating > 5) { Swal.fire('Lưu ý!', 'Vui lòng chọn số sao đánh giá.', 'warning'); return; }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang xử lý...';

        fetch("{{ route('reviews.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: JSON.stringify({ order_id: orderId, order_item_id: itemId, rating, comment })
        }).then(r => r.json()).then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('reviewModal'))?.hide();
                Swal.fire({ title: 'Cảm ơn bạn!', text: data.message, icon: 'success', confirmButtonText: 'Đóng', confirmButtonColor: '#22c55e' })
                    .then(() => window.location.reload());
            } else {
                Swal.fire('Lỗi!', data.message, 'error');
                btn.disabled = false;
                btn.innerHTML = 'Hoàn Tất Gửi <i class="fa-solid fa-paper-plane ms-2 fa-sm"></i>';
            }
        }).catch(() => {
            Swal.fire('Lỗi!', 'Không thể kết nối máy chủ.', 'error');
            btn.disabled = false;
            btn.innerHTML = 'Hoàn Tất Gửi <i class="fa-solid fa-paper-plane ms-2 fa-sm"></i>';
        });
    });

    document.querySelectorAll('.btn-cancel').forEach(btn => {
        btn.addEventListener('click', function () {
            const formId = this.getAttribute('data-form-id');
            const form   = document.querySelector(`.cancel-form[data-order-id="${String(formId).padStart(6, '0')}"]`);
            const orderId = form.getAttribute('data-order-id');
            Swal.fire({
                title: 'Hủy Đơn Hàng',
                html: `Bạn có chắc muốn hủy đơn <strong>#DH${orderId}</strong>?<br><span class="text-danger small">Lưu ý: Không thể hoàn tác sau khi xác nhận.</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Đồng Ý Hủy',
                cancelButtonText: 'Giữ Lại Đơn',
                reverseButtons: true
            }).then(result => {
                if (result.isConfirmed) {
                    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i>Đang xử lý';
                    btn.disabled = true;
                    form.submit();
                }
            });
        });
    });
});
</script>

@endsection
