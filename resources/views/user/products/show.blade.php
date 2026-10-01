@extends('user.layout')
@section('title', $product->name . ' — WashingStore')

@section('content')

<style>
    /* ── VARIANT RADIO CARDS ── */
    .variant-radio { display: none; }
    .variant-card {
        border: 2px solid #e2e8f0;
        background: #f8faff;
        border-radius: 14px;
        padding: .75rem 1rem;
        cursor: pointer;
        transition: all .22s cubic-bezier(.25,.8,.25,1);
        position: relative;
        overflow: hidden;
        text-align: center;
        min-width: 110px;
    }
    .variant-card:hover:not(.disabled-card) {
        border-color: #93c5fd;
        background: #fff;
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(37,99,235,.1);
    }
    .variant-radio:checked + .variant-card {
        border-color: #2563eb;
        background: #eff6ff;
        box-shadow: 0 4px 20px rgba(37,99,235,.15);
    }
    .variant-radio:checked + .variant-card::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        top: 0; right: 0;
        background: #2563eb;
        color: #fff;
        font-size: 9px;
        padding: 3px 7px;
        border-bottom-left-radius: 8px;
    }
    .disabled-card { opacity: .55; cursor: not-allowed !important; filter: grayscale(70%); }

    /* ── IMAGE GALLERY ── */
    .main-img-frame {
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        display: flex; align-items: center; justify-content: center;
        height: 440px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .thumbnail-img {
        width: 72px; height: 72px;
        object-fit: contain;
        background: #fff;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 5px;
        cursor: pointer;
        opacity: .6;
        transition: all .25s;
    }
    .thumbnail-img:hover { opacity: 1; transform: translateY(-2px); border-color: #93c5fd; }
    .thumbnail-img.active { opacity: 1; border-color: #2563eb; box-shadow: 0 4px 12px rgba(37,99,235,.2); }

    /* ── WISHLIST BTN ── */
    .wishlist-btn {
        position: absolute; top: 18px; right: 18px; z-index: 10;
        width: 42px; height: 42px;
        border-radius: 50%;
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(8px);
        border: 1px solid #e2e8f0;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        transition: all .2s;
    }
    .wishlist-btn:hover { background: #fff; transform: scale(1.1); box-shadow: 0 4px 12px rgba(239,68,68,.2); }

    /* ── ADD TO CART ── */
    .btn-add-cart {
        background: #2563eb;
        color: #fff;
        border: none;
        border-radius: 50px;
        padding: .85rem 1.75rem;
        font-size: .95rem;
        font-weight: 700;
        transition: all .3s ease;
        letter-spacing: .3px;
    }
    .btn-add-cart:hover {
        background: #1d4ed8;
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(37,99,235,.3) !important;
        color: #fff;
    }
    .btn-view-cart {
        border: 2px solid #2563eb;
        color: #2563eb;
        background: transparent;
        border-radius: 50px;
        padding: .85rem 1.75rem;
        font-size: .95rem;
        font-weight: 600;
        transition: all .25s;
    }
    .btn-view-cart:hover { background: #eff6ff; }

    /* ── PRICE BOX ── */
    .price-box {
        background: linear-gradient(135deg,#fff7ed,#fff);
        border: 1.5px solid #fed7aa;
        border-radius: 18px;
        padding: 1.25rem 1.5rem;
        display: flex; align-items: center; flex-wrap: wrap; gap: .75rem;
    }
    .price-display {
        font-size: 2.4rem;
        font-weight: 800;
        color: #ef4444;
        letter-spacing: -1px;
        line-height: 1.1;
    }

    /* ── STAR RATING ── */
    .star-interactive i {
        cursor: pointer;
        font-size: 1.5rem;
        color: #f59e0b;
        transition: transform .15s;
    }
    .star-interactive i:hover { transform: scale(1.2); }

    /* ── REVIEW CARD ── */
    .review-item {
        padding: 1.25rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .review-item:last-child { border-bottom: none; }
    .reviewer-avatar {
        width: 44px; height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg,#2563eb,#0ea5e9);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; font-weight: 700;
        flex-shrink: 0;
    }
    .review-stars { color: #f59e0b; font-size: .9rem; }
    .review-text {
        background: #f8faff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: .75rem 1rem;
        font-size: .9rem;
        color: #475569;
        margin-top: .5rem;
        line-height: 1.65;
    }

    /* ── TRUST ICONS ── */
    .trust-icon-box {
        text-align: center;
        padding: .75rem .5rem;
    }
    .trust-icon-box i {
        font-size: 1.6rem;
        margin-bottom: .5rem;
    }
    .trust-icon-box .label {
        font-size: .78rem;
        font-weight: 600;
        color: #334155;
        line-height: 1.35;
    }

    /* ── REVIEW FORM ── */
    .review-form-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 1.75rem;
    }

    /* ── RATINGS OVERVIEW ── */
    .ratings-overview {
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2rem;
        text-align: center;
    }
    .rating-big { font-size: 4rem; font-weight: 800; color: #0f172a; line-height: 1; }
    .rating-stars-big { color: #f59e0b; font-size: 1.35rem; margin: .5rem 0; }
</style>

<div class="container py-5 pb-4">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb" style="font-size:.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('welcome') }}" class="text-decoration-none text-muted"><i class="fa-solid fa-house fa-xs me-1"></i>Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('user.products.index') }}" class="text-decoration-none text-muted">Sản phẩm</a></li>
            <li class="breadcrumb-item active text-dark fw-500" style="font-weight:500;">{{ $product->name }}</li>
        </ol>
    </nav>

    {{-- ── 2-COL LAYOUT ── --}}
    <div class="row g-5 mb-5">

        {{-- LEFT: Image Gallery --}}
        <div class="col-lg-5">
            <div class="sticky-top" style="top:95px;">
                {{-- Main image --}}
                <div class="main-img-frame mb-3">
                    {{-- Wishlist --}}
                    @php $isWishlisted = Auth::check() && Auth::user()->wishlists()->where('product_id', $product->id)->exists(); @endphp
                    <button type="button" class="wishlist-btn toggle-wishlist-btn" data-id="{{ $product->id }}" title="{{ $isWishlisted ? 'Bỏ thích' : 'Yêu thích' }}">
                        <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular text-muted' }} fa-heart" style="font-size:1.1rem;"></i>
                    </button>

                    <img id="main-product-img"
                         src="{{ $product->image_url }}"
                         alt="{{ $product->name }}"
                         class="img-fluid"
                         style="max-height:100%;max-width:100%;object-fit:contain;transition:opacity .3s ease;">
                </div>

                {{-- Thumbnails --}}
                @if($product->variants->isNotEmpty())
                    <div class="d-flex flex-wrap gap-2 justify-content-center" id="thumbnail-gallery">
                        <img src="{{ $product->image_url }}"
                             class="thumbnail-img active"
                             data-src="{{ $product->image_url }}"
                             onclick="switchImage(this)" alt="Ảnh chính">
                        @foreach($product->variants as $v)
                            <img src="{{ $v->image_url }}"
                                 alt="{{ $v->color_name }}"
                                 class="thumbnail-img"
                                 data-src="{{ $v->image_url }}"
                                 title="{{ $v->color_name }}"
                                 onclick="switchImage(this)">
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT: Product Info --}}
        <div class="col-lg-7">

            {{-- Badges --}}
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge px-3 py-2 rounded-pill" style="font-size:.78rem;font-weight:600;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;">
                    {{ $product->category->name ?? 'N/A' }}
                </span>
                <span class="badge px-3 py-2 rounded-pill" style="font-size:.78rem;font-weight:600;background:#f8faff;color:#64748b;border:1px solid #e2e8f0;">
                    <i class="fa-solid fa-tag fa-xs me-1"></i>{{ $product->brand }}
                </span>
                @if($product->capacity_kg)
                    <span class="badge px-3 py-2 rounded-pill" style="font-size:.78rem;font-weight:600;background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;">
                        <i class="fa-solid fa-weight-hanging fa-xs me-1"></i>{{ $product->capacity_kg }} kg
                    </span>
                @endif
            </div>

            {{-- Product Name --}}
            <h1 class="fw-800 text-dark mb-3" style="font-size:1.85rem;font-weight:800;letter-spacing:-.5px;line-height:1.25;">
                {{ $product->name }}
            </h1>

            {{-- Quick Rating --}}
            <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
                @php
                    $avgRating   = $product->avgRating();
                    $totalReview = $product->reviews()->count();
                @endphp
                <div class="d-flex align-items-center gap-1">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fa-{{ $i <= round($avgRating) ? 'solid' : 'regular' }} fa-star text-warning" style="font-size:.95rem;"></i>
                    @endfor
                    <span class="fw-700 ms-1" style="font-size:.9rem;font-weight:700;">{{ number_format($avgRating,1) }}</span>
                </div>
                <span class="text-muted" style="font-size:.85rem;">{{ $totalReview }} đánh giá</span>
                <span class="text-muted" style="font-size:.85rem;">Đã bán: <strong class="text-dark">{{ $product->totalSold() ?? 0 }}</strong></span>
            </div>

            {{-- Price Box --}}
            <div class="price-box mb-4">
                <span id="display-price" class="price-display">
                    {{ number_format($product->price, 0, ',', '.') }} đ
                </span>
                @if($product->variants->isNotEmpty())
                    <span class="badge px-3 py-2 rounded-pill" style="font-size:.8rem;font-weight:600;background:#fff7ed;color:#ea580c;border:1px solid #fed7aa;">
                        <i class="fa-solid fa-bolt me-1"></i> Giá thay đổi theo màu
                    </span>
                @endif
            </div>

            {{-- Description --}}
            <p class="mb-4 text-muted" style="font-size:.95rem;line-height:1.8;">
                {{ $product->description ?? 'Chưa có mô tả chi tiết cho sản phẩm này.' }}
            </p>

            {{-- ── VARIANTS ── --}}
            @if($product->variants->isNotEmpty())
                <div class="mb-5">
                    <p class="fw-700 mb-3 text-dark" style="font-weight:700;font-size:.9rem;letter-spacing:.5px;text-transform:uppercase;">
                        Chọn phiên bản / màu sắc:
                    </p>
                    <div class="d-flex flex-wrap gap-3" id="variant-selector">
                        @foreach($product->variants as $variant)
                            <label class="variant-label m-0" for="variant_{{ $variant->id }}" style="cursor:{{ $variant->stock <= 0 ? 'not-allowed' : 'pointer' }};">
                                <input type="radio" name="variant_radio" id="variant_{{ $variant->id }}"
                                       value="{{ $variant->id }}"
                                       data-price="{{ $variant->price }}"
                                       data-stock="{{ $variant->stock }}"
                                       data-name="{{ $variant->color_name }}"
                                       data-img="{{ $variant->image ? asset('storage/' . $variant->image) : '' }}"
                                       class="variant-radio"
                                       {{ $variant->stock <= 0 ? 'disabled' : '' }}>
                                <div class="variant-card {{ $variant->stock <= 0 ? 'disabled-card' : '' }}">
                                    @if($variant->color_code)
                                        <span class="d-block rounded-circle mx-auto mb-2 border-2 shadow-sm"
                                              style="width:26px;height:26px;background:{{ $variant->color_code }};border:2px solid rgba(0,0,0,.1);"></span>
                                    @endif
                                    <div class="fw-700 text-dark mb-1" style="font-size:.82rem;font-weight:700;">{{ $variant->color_name }}</div>
                                    <div style="color:#2563eb;font-size:.8rem;font-weight:600;">{{ number_format($variant->price, 0, ',', '.') }} đ</div>
                                    @if($variant->stock <= 0)
                                        <div class="text-danger mt-1" style="font-size:.72rem;font-weight:700;">Hết hàng</div>
                                    @else
                                        <div class="text-success mt-1" style="font-size:.72rem;font-weight:600;">Còn {{ $variant->stock }}</div>
                                    @endif
                                </div>
                            </label>
                        @endforeach
                    </div>
                    <div id="variant-error" class="text-danger mt-2" style="font-size:.85rem;font-weight:600;display:none;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Vui lòng chọn màu sắc trước khi thêm vào giỏ.
                    </div>
                </div>
            @else
                <div class="mb-4">
                    @if($product->stock_quantity > 0)
                        <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;font-weight:600;font-size:.88rem;">
                            <i class="fa-solid fa-circle-check"></i> Sẵn sàng giao hàng — Còn <strong>{{ $product->stock_quantity }}</strong> chiếc
                        </div>
                    @else
                        <div class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill" style="background:#fef2f2;border:1px solid #fecaca;color:#ef4444;font-weight:600;font-size:.88rem;">
                            <i class="fa-solid fa-ban"></i> Hiện đang hết hàng
                        </div>
                    @endif
                </div>
            @endif

            {{-- ── ADD TO CART + TRUST ── --}}
            <div class="bg-white border rounded-4 p-4" style="border-color:#e2e8f0 !important;">
                @auth
                    @if($product->stock_quantity > 0 || $product->variants->where('stock', '>', 0)->count() > 0)
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" id="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="variant_id" id="selected-variant-id" value="">
                            <div class="d-flex flex-wrap gap-3">
                                <button type="button" id="btn-add-cart" class="btn-add-cart flex-grow-1">
                                    <i class="fa-solid fa-cart-plus me-2"></i>Thêm Vào Giỏ
                                </button>
                                <a href="{{ route('cart.index') }}" class="btn-view-cart flex-grow-1 d-flex align-items-center justify-content-center gap-2 text-decoration-none">
                                    <i class="fa-solid fa-bag-shopping"></i> Đến Giỏ Hàng
                                </a>
                            </div>
                        </form>
                    @else
                        <button class="btn w-100 rounded-pill fw-700 py-3" style="background:#f1f5f9;color:#94a3b8;border:none;cursor:not-allowed;font-weight:700;" disabled>
                            <i class="fa-solid fa-ban me-2"></i>Sản Phẩm Đã Hết Hàng
                        </button>
                    @endif
                @else
                    <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#fffbeb;border:1px solid #fde68a;">
                        <i class="fa-solid fa-lock text-warning fa-lg"></i>
                        <div>
                            <div class="fw-700 text-dark mb-1" style="font-weight:700;font-size:.9rem;">Yêu cầu đăng nhập</div>
                            <span style="font-size:.85rem;color:#78716c;">
                                Vui lòng <a href="{{ route('login') }}" class="fw-700 text-decoration-none text-warning">Đăng nhập</a>
                                hoặc <a href="{{ route('register') }}" class="fw-700 text-decoration-none text-warning">Đăng ký</a> để mua hàng.
                            </span>
                        </div>
                    </div>
                @endauth

                {{-- Trust icons --}}
                <div class="row g-0 mt-4 pt-4 border-top" style="border-color:#f1f5f9 !important;">
                    <div class="col-4">
                        <div class="trust-icon-box">
                            <i class="fa-solid fa-award text-primary"></i>
                            <div class="label">Hàng Chính Hãng<br>100%</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="trust-icon-box">
                            <i class="fa-solid fa-truck-fast text-success"></i>
                            <div class="label">Miễn Phí<br>Giao Hàng</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="trust-icon-box">
                            <i class="fa-solid fa-screwdriver-wrench text-warning"></i>
                            <div class="label">Bảo Hành<br>24 Tháng</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JS: Variant + AddToCart + switchImage --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const radios      = document.querySelectorAll('.variant-radio');
        const priceDisplay= document.getElementById('display-price');
        const hiddenInput = document.getElementById('selected-variant-id');
        const btnAdd      = document.getElementById('btn-add-cart');
        const form        = document.getElementById('add-to-cart-form');
        const errDiv      = document.getElementById('variant-error');
        const mainImg     = document.getElementById('main-product-img');

        function formatVND(n) { return new Intl.NumberFormat('vi-VN').format(n) + ' đ'; }

        radios.forEach(function (radio) {
            radio.closest('.variant-label').addEventListener('click', function () {
                if (radio.disabled) return;
                radio.checked = true;
                if (priceDisplay) priceDisplay.textContent = formatVND(radio.getAttribute('data-price'));
                if (hiddenInput) hiddenInput.value = radio.value;
                const imgSrc = radio.getAttribute('data-img');
                if (mainImg && mainImg.tagName === 'IMG' && imgSrc) {
                    mainImg.style.opacity = '0';
                    setTimeout(() => { mainImg.src = imgSrc; mainImg.style.opacity = '1'; }, 150);
                }
                if (errDiv) errDiv.style.display = 'none';
            });
        });

        if (btnAdd) {
            btnAdd.addEventListener('click', function () {
                if (radios.length > 0 && !hiddenInput.value) {
                    errDiv.style.display = 'block';
                    errDiv.animate([
                        { transform: 'translateX(0)' }, { transform: 'translateX(-5px)' },
                        { transform: 'translateX(5px)' }, { transform: 'translateX(0)' }
                    ], { duration: 300, iterations: 2 });
                    return;
                }
                btnAdd.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang xử lý...';
                btnAdd.disabled = true;
                form.submit();
            });
        }
    });

    function switchImage(el) {
        const mainImg = document.getElementById('main-product-img');
        if (mainImg && mainImg.tagName === 'IMG') {
            mainImg.style.opacity = '0';
            setTimeout(() => { mainImg.src = el.getAttribute('data-src'); mainImg.style.opacity = '1'; }, 150);
        }
        document.querySelectorAll('.thumbnail-img').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
    }
</script>

{{-- ── REVIEWS SECTION ── --}}
<section class="py-5 border-top" style="background:#f8faff;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h2 class="fw-800 text-center mb-5" style="font-size:1.65rem;font-weight:800;letter-spacing:-.5px;">
                    <i class="fa-solid fa-comments text-primary me-2"></i>Đánh Giá &amp; Nhận Xét
                </h2>

                <div class="row g-4 mb-5">
                    {{-- Rating Overview --}}
                    <div class="col-md-4">
                        <div class="ratings-overview h-100 d-flex flex-column align-items-center justify-content-center">
                            <div class="text-muted mb-1" style="font-size:.8rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;">Điểm trung bình</div>
                            <div class="rating-big">{{ number_format($avgRating, 1) }}</div>
                            <div class="rating-stars-big">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-{{ $i <= round($avgRating) ? 'solid' : 'regular' }} fa-star"></i>
                                @endfor
                            </div>
                            <div class="text-muted" style="font-size:.85rem;font-weight:500;">{{ $totalReview }} nhận xét</div>
                        </div>
                    </div>

                    {{-- Review Form --}}
                    <div class="col-md-8">
                        <div class="review-form-card h-100">
                            <h5 class="fw-700 mb-4 text-dark" style="font-weight:700;">Gửi đánh giá của bạn</h5>
                            @auth
                                <form action="{{ route('reviews.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="mb-4">
                                        <label class="fw-600 text-dark mb-2 d-block" style="font-weight:600;font-size:.88rem;">Chất lượng sản phẩm:</label>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="star-interactive" id="review-stars">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fa-solid fa-star" data-rating="{{ $i }}"></i>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="rating-input" value="5">
                                            <span class="fw-700 text-success ms-2" id="rating-text" style="font-size:.88rem;font-weight:700;">Tuyệt vời!</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="fw-600 text-dark mb-2 d-block" style="font-weight:600;font-size:.88rem;">Nội dung đánh giá:</label>
                                        <textarea class="form-control shadow-none" name="comment" rows="4"
                                                  placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..."
                                                  style="border:1.5px solid #e2e8f0;border-radius:14px;font-size:.9rem;resize:none;"
                                                  required></textarea>
                                    </div>

                                    <button type="submit" class="btn fw-600 px-5 rounded-pill shadow-sm"
                                            style="background:#2563eb;color:#fff;font-weight:600;">
                                        Gửi Đánh Giá <i class="fa-solid fa-paper-plane ms-2 fa-sm"></i>
                                    </button>
                                </form>

                                <script>
                                    const stars = document.querySelectorAll('#review-stars .fa-star');
                                    const ratingInput = document.getElementById('rating-input');
                                    const ratingText  = document.getElementById('rating-text');
                                    const ratingColors = ['#ef4444','#f97316','#eab308','#22c55e','#16a34a'];
                                    const ratingLabels = ['Rất tệ 😞','Không hài lòng 😕','Bình thường 😐','Hài lòng 😊','Tuyệt vời! 🌟'];

                                    stars.forEach(star => {
                                        star.addEventListener('click', function() {
                                            const r = this.getAttribute('data-rating');
                                            ratingInput.value = r;
                                            ratingText.textContent = ratingLabels[r-1];
                                            ratingText.style.color = ratingColors[r-1];
                                            stars.forEach(s => {
                                                s.classList.toggle('fa-solid', s.getAttribute('data-rating') <= r);
                                                s.classList.toggle('fa-regular', s.getAttribute('data-rating') > r);
                                            });
                                        });
                                        star.addEventListener('mouseenter', function() {
                                            const r = this.getAttribute('data-rating');
                                            stars.forEach(s => {
                                                s.style.opacity = s.getAttribute('data-rating') <= r ? '1' : '0.45';
                                            });
                                        });
                                    });
                                    document.getElementById('review-stars').addEventListener('mouseleave', () => {
                                        stars.forEach(s => s.style.opacity = '1');
                                    });
                                </script>
                            @else
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3" style="background:#eff6ff;border:1px solid #bfdbfe;">
                                    <i class="fa-solid fa-circle-info text-primary fa-lg"></i>
                                    <div style="font-size:.9rem;">
                                        Bạn cần <a href="{{ route('login') }}" class="fw-700 text-decoration-none text-primary">Đăng nhập</a>
                                        để viết đánh giá cho sản phẩm này.
                                    </div>
                                </div>
                            @endauth
                        </div>
                    </div>
                </div>

                {{-- Review List --}}
                <div class="bg-white border rounded-4 p-4" style="border-color:#e2e8f0 !important;">
                    <h5 class="fw-700 mb-0 d-flex align-items-center gap-2" style="font-weight:700;">
                        <i class="fa-solid fa-message text-primary fa-sm"></i>
                        Khách Hàng Nói Gì?
                        <span class="badge ms-auto" style="font-size:.75rem;font-weight:600;background:#eff6ff;color:#2563eb;">{{ $totalReview }} đánh giá</span>
                    </h5>

                    <hr style="border-color:#f1f5f9;margin:1rem 0;">

                    @php
                        $reviews = $product->reviews()->with('user')->latest()->take(10)->get();
                    @endphp

                    @forelse($reviews as $review)
                        <div class="review-item">
                            <div class="d-flex gap-3">
                                <div class="reviewer-avatar">
                                    {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="fw-700 text-dark" style="font-size:.9rem;font-weight:700;">{{ $review->user->name ?? 'Người dùng ẩn danh' }}</span>
                                        <span class="text-muted" style="font-size:.78rem;">
                                            <i class="fa-regular fa-clock me-1"></i>{{ $review->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <div class="review-stars mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa-{{ $i <= $review->rating ? 'solid' : 'regular' }} fa-star"></i>
                                        @endfor
                                        <span class="ms-1 fw-600 text-warning" style="font-size:.8rem;font-weight:600;">{{ $review->rating }}/5</span>
                                    </div>
                                    <div class="review-text">
                                        {{ $review->comment ?? 'Khách hàng không để lại nội dung đánh giá.' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="fa-regular fa-comment-dots fa-3x text-muted opacity-40 mb-3"></i>
                            <h6 class="fw-700 text-muted" style="font-weight:700;">Chưa có lời nhận xét nào.</h6>
                            <p class="text-muted mb-0" style="font-size:.88rem;">Hãy là người đầu tiên trải nghiệm và chia sẻ nhé!</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
</section>

@endsection