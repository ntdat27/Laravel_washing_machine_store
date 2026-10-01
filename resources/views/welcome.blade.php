@extends('user.layout')
@section('title', 'WashingStore - Siêu Thị Máy Giặt Chính Hãng')

@section('content')

<style>
    /* ── HERO ── */
    .hero-section {
        background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 50%, #0ea5e9 100%);
        position: relative;
        overflow: hidden;
        min-height: 480px;
        display: flex;
        align-items: center;
    }
    .hero-section::before {
        content: '';
        position: absolute; inset: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Ccircle cx='30' cy='30' r='20'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .hero-badge {
        display: inline-flex; align-items: center; gap: .4rem;
        background: rgba(255,255,255,.15);
        border: 1px solid rgba(255,255,255,.25);
        border-radius: 50px;
        padding: .4rem 1rem;
        font-size: .85rem; font-weight: 600;
        color: rgba(255,255,255,.95);
        margin-bottom: 1.5rem;
        backdrop-filter: blur(8px);
    }
    .hero-cta {
        background: #fff;
        color: #2563eb;
        border: none;
        border-radius: 50px;
        padding: .8rem 2rem;
        font-size: 1rem;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex; align-items: center; gap: .5rem;
        box-shadow: 0 8px 24px rgba(0,0,0,.15);
        transition: all .25s ease;
    }
    .hero-cta:hover {
        transform: translateY(-3px);
        box-shadow: 0 16px 32px rgba(0,0,0,.2);
        color: #1d4ed8;
    }
    .hero-cta-outline {
        background: rgba(255,255,255,.12);
        color: #fff;
        border: 2px solid rgba(255,255,255,.5);
        border-radius: 50px;
        padding: .8rem 1.8rem;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex; align-items: center; gap: .5rem;
        transition: all .25s ease;
        backdrop-filter: blur(8px);
    }
    .hero-cta-outline:hover { background: rgba(255,255,255,.22); color: #fff; transform: translateY(-2px); }
    .hero-stat { text-align: center; }
    .hero-stat-num { font-size: 1.8rem; font-weight: 800; color: #fff; line-height: 1.1; }
    .hero-stat-label { font-size: .78rem; color: rgba(255,255,255,.65); font-weight: 500; }
    .hero-deco {
        position: absolute;
        opacity: .06;
        font-size: 300px;
        right: -40px; top: -60px;
        color: #fff;
        transform: rotate(12deg);
        pointer-events: none;
    }

    /* ── SECTION HEADER ── */
    .section-eyebrow {
        font-size: .78rem; font-weight: 700; letter-spacing: 2px;
        text-transform: uppercase; color: #2563eb;
        display: flex; align-items: center; gap: .4rem;
        margin-bottom: .4rem;
    }
    .section-title { font-size: 1.75rem; font-weight: 800; letter-spacing: -.5px; color: #0f172a; margin-bottom: .5rem; }
    .section-sub { color: #64748b; font-size: .95rem; }

    /* ── TRUST BAR ── */
    .trust-bar {
        background: #fff;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
    }
    .trust-item {
        display: flex; align-items: center; gap: .75rem;
        padding: 1rem 0;
        font-size: .88rem; font-weight: 500; color: #334155;
    }
    .trust-item i { color: #2563eb; font-size: 1.25rem; flex-shrink: 0; }

    /* ── PRODUCT CARD (Shared via layout) ── */
    @media (min-width: 992px) { .col-lg-2dot4 { flex: 0 0 auto; width: 20%; } }

    /* ── TOP BADGE ── */
    .rank-badge {
        position: absolute; top: 14px; left: -1px;
        padding: .3rem .8rem;
        border-radius: 0 6px 6px 0;
        font-size: .75rem; font-weight: 700;
        color: #fff;
    }
</style>

<!-- ── HERO BANNER ── -->
<div class="hero-section py-5">
    <i class="fa-solid fa-washing-machine hero-deco"></i>
    <div class="container position-relative z-1 py-3">
        <div class="row align-items-center gy-4">
            <div class="col-lg-8">
                <div class="hero-badge">
                    <i class="fa-solid fa-bolt"></i> Sản phẩm chính hãng 100%
                </div>
                <h1 class="display-5 fw-bold text-white mb-3" style="letter-spacing:-.5px;line-height:1.15;">
                    Hệ Thống Phân Phối<br>Máy Giặt Chính Hãng
                </h1>
                <p class="mb-4" style="font-size:1.05rem;color:rgba(255,255,255,.75);max-width:520px;line-height:1.7;">
                    Sản phẩm chất lượng — Bảo hành tận tâm — Giao hàng nhanh toàn quốc. Đồng hành cùng hàng triệu gia đình Việt.
                </p>
                <div class="d-flex flex-wrap gap-3">
                    <a href="#product-list" class="hero-cta">
                        <i class="fa-solid fa-arrow-down"></i> Khám phá ngay
                    </a>
                    <a href="{{ route('user.products.index') }}" class="hero-cta-outline">
                        Tất cả sản phẩm <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <div class="d-flex justify-content-end gap-4 pe-2">
                    <div class="hero-stat">
                        <div class="hero-stat-num">2000+</div>
                        <div class="hero-stat-label">Sản phẩm</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">50K+</div>
                        <div class="hero-stat-label">Khách hàng</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-num">24T</div>
                        <div class="hero-stat-label">Bảo hành</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── TRUST BAR ── -->
<div class="trust-bar">
    <div class="container">
        <div class="row gy-0 row-cols-2 row-cols-md-4">
            <div class="col"><div class="trust-item"><i class="fa-solid fa-shield-halved"></i> Hàng chính hãng 100%</div></div>
            <div class="col"><div class="trust-item"><i class="fa-solid fa-truck-fast"></i> Miễn phí giao hàng</div></div>
            <div class="col"><div class="trust-item"><i class="fa-solid fa-rotate-left"></i> Đổi trả 7 ngày</div></div>
            <div class="col"><div class="trust-item"><i class="fa-solid fa-headset"></i> Hỗ trợ 7/24</div></div>
        </div>
    </div>
</div>

<!-- ── SECTION: SẢN PHẨM BÁN CHẠY ── -->
@if($bestSellers->isNotEmpty())
<section class="py-5 mt-3">
    <div class="container">
        <div class="d-flex flex-column align-items-center text-center mb-5">
            <div class="section-eyebrow">
                <i class="fa-solid fa-fire text-danger"></i> Bán chạy nhất
            </div>
            <h2 class="section-title">Được Khách Hàng Tin Dùng Nhất</h2>
            <p class="section-sub">Lựa chọn hàng đầu cho không gian giặt giũ của gia đình bạn</p>
        </div>

        <div class="row g-4 justify-content-center">
            @foreach($bestSellers as $index => $best)
                <div class="col-6 col-md-4 col-lg-2dot4">
                    <div class="card h-100 product-card position-relative border-0 shadow-sm">
                        {{-- Rank Badge --}}
                        @if($index === 0)
                            <span class="rank-badge" style="background:linear-gradient(135deg,#f59e0b,#d97706);">
                                <i class="fa-solid fa-crown me-1"></i>Top 1
                            </span>
                        @elseif($index === 1)
                            <span class="rank-badge bg-secondary">
                                <i class="fa-solid fa-medal me-1"></i>Top 2
                            </span>
                        @elseif($index === 2)
                            <span class="rank-badge" style="background:#b45309;">
                                <i class="fa-solid fa-medal me-1"></i>Top 3
                            </span>
                        @else
                            <span class="rank-badge bg-dark">Top {{ $index + 1 }}</span>
                        @endif

                        {{-- Wishlist --}}
                        @php $isWishlisted = Auth::check() && Auth::user()->wishlists()->where('product_id', $best->id)->exists(); @endphp
                        <button type="button" class="position-absolute z-3 toggle-wishlist-btn"
                                style="top:14px;right:14px;" data-id="{{ $best->id }}"
                                title="{{ $isWishlisted ? 'Bỏ thích' : 'Yêu thích' }}">
                            <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular text-muted' }} fa-heart fa-sm"></i>
                        </button>

                        <a href="{{ route('user.products.show', $best->id) }}" class="d-block text-decoration-none">
                            <div class="product-img-wrapper">
                                @if($best->image)
                                    <img src="{{ asset('storage/' . $best->image) }}" alt="{{ $best->name }}" class="product-img">
                                @else
                                    <i class="fa-solid fa-washing-machine fa-4x text-secondary opacity-25"></i>
                                @endif
                            </div>
                        </a>

                        <div class="card-body px-3 pb-3 pt-2 d-flex flex-column text-center">
                            <h6 class="fw-700 text-dark mb-2" style="font-size:.88rem;font-weight:700;min-height:2.6rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                                <a href="{{ route('user.products.show', $best->id) }}" class="text-dark text-decoration-none">
                                    {{ $best->name }}
                                </a>
                            </h6>
                            <div class="mt-auto">
                                <div class="text-danger fw-800 mb-1" style="font-size:1.1rem;font-weight:800;letter-spacing:-.5px;">
                                    {{ number_format($best->price, 0, ',', '.') }} đ
                                </div>
                                @if($best->total_sold > 0)
                                    <div class="text-muted mb-2" style="font-size:.78rem;">
                                        <i class="fa-solid fa-bag-shopping me-1 text-primary"></i>Đã bán: <strong>{{ $best->total_sold }}</strong>
                                    </div>
                                @endif
                                <a href="{{ route('user.products.show', $best->id) }}"
                                   class="btn btn-sm w-100 fw-600 rounded-pill shadow-sm"
                                   style="background:#2563eb;color:#fff;font-weight:600;font-size:.82rem;">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- ── SECTION: TẤT CẢ SẢN PHẨM ── -->
<section class="py-5 bg-white border-top" id="product-list">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5 flex-wrap gap-3">
            <div>
                <div class="section-eyebrow"><i class="fa-solid fa-tags"></i> Bộ sưu tập</div>
                <h2 class="section-title mb-1">Sản Phẩm Mới &amp; Nổi Bật</h2>
                <p class="section-sub mb-0">Khám phá các dòng máy giặt được cập nhật liên tục</p>
            </div>
            <a href="{{ route('user.products.index') }}"
               class="btn btn-outline-primary rounded-pill fw-600 px-4 d-none d-md-inline-flex align-items-center gap-2"
               style="font-weight:600;">
                Xem tất cả <i class="fa-solid fa-arrow-right fa-sm"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($products as $product)
                <div class="col-12 col-md-6 col-lg-3">
                    <div class="card h-100 product-card position-relative border-0 shadow-sm">
                        @if($product->created_at >= now()->subDays(7))
                            <div class="position-absolute z-2" style="top:14px;right:14px;">
                                <span class="badge rounded-pill px-2 py-1" style="font-size:.72rem;font-weight:700;background:#22c55e;color:#fff;">MỚI</span>
                            </div>
                        @endif

                        @php $isWishlisted = Auth::check() && Auth::user()->wishlists()->where('product_id', $product->id)->exists(); @endphp
                        <button type="button" class="position-absolute z-3 toggle-wishlist-btn"
                                style="top:14px;right:{{ $product->created_at >= now()->subDays(7) ? '74px' : '14px' }};"
                                data-id="{{ $product->id }}" title="{{ $isWishlisted ? 'Bỏ thích' : 'Yêu thích' }}">
                            <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular text-muted' }} fa-heart fa-sm"></i>
                        </button>

                        <a href="{{ route('user.products.show', $product->id) }}" class="d-block text-decoration-none">
                            <div class="product-img-wrapper">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="product-img">
                            </div>
                        </a>

                        <div class="card-body p-3 d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge px-2 py-1" style="font-size:.72rem;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:6px;">
                                    {{ $product->category->name ?? 'Máy giặt' }}
                                </span>
                                <span class="text-muted" style="font-size:.75rem;font-weight:500;">{{ $product->brand }}</span>
                            </div>
                            <h6 class="fw-700 text-dark mb-2" style="font-weight:700;font-size:.9rem;min-height:2.5rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" title="{{ $product->name }}">
                                <a href="{{ route('user.products.show', $product->id) }}" class="text-dark text-decoration-none">{{ $product->name }}</a>
                            </h6>

                            <div class="mt-auto pt-3 border-top" style="border-color:#f1f5f9 !important;">
                                <div class="text-danger fw-800 mb-3" style="font-size:1.15rem;font-weight:800;letter-spacing:-.5px;">
                                    {{ number_format($product->price, 0, ',', '.') }} đ
                                </div>
                                <div class="d-grid gap-2 d-xl-flex">
                                    <a href="{{ route('user.products.show', $product->id) }}"
                                       class="btn btn-sm fw-600 w-100 rounded-pill"
                                       style="font-weight:600;border:1.5px solid #2563eb;color:#2563eb;">
                                        Chi Tiết
                                    </a>
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-100">
                                        @csrf
                                        <button type="submit"
                                                class="btn btn-sm fw-600 w-100 rounded-pill shadow-sm"
                                                style="background:#2563eb;color:#fff;font-weight:600;">
                                            <i class="fa-solid fa-cart-plus fa-sm"></i> Thêm
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fa-solid fa-box-open fa-3x text-muted opacity-50 mb-3"></i>
                        <h5 class="fw-bold text-dark">Chưa có sản phẩm</h5>
                        <p class="text-muted">Hệ thống đang cập nhật sản phẩm. Vui lòng quay lại sau.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5 d-md-none">
            <a href="{{ route('user.products.index') }}" class="btn rounded-pill fw-600 px-5 py-2" style="border:1.5px solid #2563eb;color:#2563eb;font-weight:600;">
                Xem tất cả sản phẩm
            </a>
        </div>
    </div>
</section>

@endsection