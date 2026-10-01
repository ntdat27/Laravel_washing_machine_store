@extends('user.layout')
@section('title', 'Danh sách sản phẩm — WashingStore')

@section('content')

<style>
    /* ── FILTER SIDEBAR ── */
    .filter-sidebar {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 1.5rem;
        position: sticky;
        top: 90px;
    }
    .filter-section-title {
        font-size: .72rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: .85rem;
    }
    /* Radio pill */
    .cat-radio-label {
        display: flex;
        align-items: center;
        gap: .6rem;
        padding: .5rem .75rem;
        border-radius: 10px;
        cursor: pointer;
        font-size: .88rem;
        font-weight: 500;
        color: #334155;
        transition: all .2s;
    }
    .cat-radio-label:hover { background: #eff6ff; color: #2563eb; }
    .cat-radio-input { display: none; }
    .cat-radio-input:checked + .cat-radio-label {
        background: #eff6ff;
        color: #2563eb;
        font-weight: 600;
    }
    .cat-radio-input:checked + .cat-radio-label::before {
        content: '';
        display: inline-block;
        width: 8px; height: 8px;
        background: #2563eb;
        border-radius: 50%;
        flex-shrink: 0;
    }

    /* Price preset */
    .price-preset {
        font-size: .8rem; font-weight: 500;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #64748b;
        background: #fff;
        padding: .35rem .6rem;
        cursor: pointer;
        transition: all .2s;
    }
    .price-preset:hover, .price-preset.active {
        background: #2563eb;
        color: #fff;
        border-color: #2563eb;
    }

    /* ── PRODUCT CARD override ── */
    .product-card {
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        overflow: hidden;
        background: #fff;
        transition: all .25s cubic-bezier(.25,.8,.25,1);
    }
    .product-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 40px rgba(0,0,0,.1) !important;
        border-color: #bfdbfe;
    }
    .product-img-wrapper {
        height: 210px;
        background: linear-gradient(135deg,#f8faff,#eff6ff);
        display: flex; align-items: center; justify-content: center;
        padding: 1.25rem;
    }
    .product-img {
        max-height: 100%; max-width: 100%;
        object-fit: contain;
        transition: transform .45s ease;
    }
    .product-card:hover .product-img { transform: scale(1.07); }

    /* ── WISHLIST BTN ── */
    .toggle-wishlist-btn {
        background: rgba(255,255,255,.9);
        backdrop-filter: blur(8px);
        border: 1px solid #e2e8f0;
        border-radius: 50%;
        width: 35px; height: 35px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; transition: all .2s;
    }
    .toggle-wishlist-btn:hover {
        background: #fff;
        box-shadow: 0 4px 12px rgba(239,68,68,.15);
        transform: scale(1.1);
    }

    /* ── ACTIVE FILTER TAGS ── */
    .filter-tag {
        display: inline-flex; align-items: center; gap: .4rem;
        padding: .35rem .85rem;
        border-radius: 50px;
        font-size: .8rem; font-weight: 600;
        cursor: pointer;
    }

    /* ── SORT DROPDOWN ── */
    .sort-select {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: .45rem 1rem;
        font-size: .88rem;
        font-weight: 500;
        color: #334155;
        background: #fff;
        cursor: pointer;
        appearance: none;
        padding-right: 2rem;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2364748b' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right .75rem center;
        transition: all .2s;
    }
    .sort-select:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,.1); }
</style>

<div class="container py-5">

    {{-- ── PAGE HEADER ── --}}
    <div class="mb-5 d-flex justify-content-between align-items-end flex-wrap gap-3">
        <div>
            <p class="text-muted mb-1" style="font-size:.8rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;">
                <i class="fa-solid fa-washing-machine text-primary me-1"></i> Danh mục
            </p>
            <h1 class="fw-800 text-dark mb-1" style="font-size:1.85rem;font-weight:800;letter-spacing:-.5px;">
                @if($search)
                    Kết quả: <span class="text-primary">"{{ $search }}"</span>
                @else
                    Khám Phá Sản Phẩm
                @endif
            </h1>
            <p class="text-muted mb-0">Tìm thấy <strong class="text-dark">{{ $products->total() }}</strong> sản phẩm phù hợp.</p>
        </div>
        {{-- Sort --}}
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-arrow-down-wide-short text-muted"></i>
            <select class="sort-select" onchange="document.getElementById('sort-input').value = this.value; document.getElementById('filter-form').submit();">
                <option value="latest"     {{ $sort === 'latest'     ? 'selected' : '' }}>Mới nhất</option>
                <option value="price_asc"  {{ $sort === 'price_asc'  ? 'selected' : '' }}>Giá tăng dần</option>
                <option value="price_desc" {{ $sort === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                <option value="name_asc"   {{ $sort === 'name_asc'   ? 'selected' : '' }}>Tên A → Z</option>
            </select>
        </div>
    </div>

    <div class="row g-5">

        {{-- ── SIDEBAR ── --}}
        <div class="col-lg-3">
            <div class="filter-sidebar">
                <div class="d-flex align-items-center gap-2 mb-4">
                    <div style="width:32px;height:32px;background:#eff6ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                        <i class="fa-solid fa-sliders text-primary fa-sm"></i>
                    </div>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size:1rem;">Bộ Lọc</h5>
                </div>

                <form action="{{ route('user.products.index') }}" method="GET" id="filter-form">
                    <input type="hidden" name="sort" id="sort-input" value="{{ $sort }}">

                    {{-- Search --}}
                    <div class="mb-4">
                        <div class="filter-section-title">Từ khóa</div>
                        <div class="input-group" style="border:1.5px solid #e2e8f0;border-radius:12px;overflow:hidden;background:#f8faff;">
                            <span class="input-group-text border-0 bg-transparent ps-3 pe-1">
                                <i class="fa-solid fa-magnifying-glass text-muted fa-sm"></i>
                            </span>
                            <input type="text" name="search" class="form-control border-0 bg-transparent shadow-none py-2"
                                   style="font-size:.88rem;" placeholder="LG, Inverter..."
                                   value="{{ $search ?? '' }}">
                        </div>
                    </div>

                    <hr class="my-3" style="border-color:#f1f5f9;">

                    {{-- Category --}}
                    <div class="mb-4">
                        <div class="filter-section-title">Thương hiệu / Danh mục</div>
                        <div class="d-flex flex-column gap-1">
                            <input type="radio" name="category_id" id="cat_all" value="" class="cat-radio-input" {{ !$categoryId ? 'checked' : '' }}>
                            <label for="cat_all" class="cat-radio-label">
                                Tất cả
                                <span class="ms-auto badge" style="background:#f1f5f9;color:#64748b;font-weight:500;font-size:.72rem;">{{ $products->total() }}</span>
                            </label>

                            @foreach($categories as $cat)
                                <input type="radio" name="category_id" id="cat_{{ $cat->id }}" value="{{ $cat->id }}" class="cat-radio-input" {{ $categoryId == $cat->id ? 'checked' : '' }}>
                                <label for="cat_{{ $cat->id }}" class="cat-radio-label">
                                    {{ $cat->name }}
                                    <span class="ms-auto badge" style="background:#f1f5f9;color:#64748b;font-weight:500;font-size:.72rem;">{{ $cat->products_count }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <hr class="my-3" style="border-color:#f1f5f9;">

                    {{-- Price --}}
                    <div class="mb-4">
                        <div class="filter-section-title">Khoảng giá (VNĐ)</div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <input type="number" name="price_min" class="form-control form-control-sm text-center shadow-none"
                                       style="border:1.5px solid #e2e8f0;border-radius:10px;font-size:.82rem;"
                                       placeholder="Từ (đ)" min="0" step="500000" value="{{ $priceMin ?? '' }}">
                            </div>
                            <div class="col-6">
                                <input type="number" name="price_max" class="form-control form-control-sm text-center shadow-none"
                                       style="border:1.5px solid #e2e8f0;border-radius:10px;font-size:.82rem;"
                                       placeholder="Đến (đ)" min="0" step="500000" value="{{ $priceMax ?? '' }}">
                            </div>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="price-preset flex-grow-1" data-min="0" data-max="5000000">Dưới 5 Tr</button>
                            <button type="button" class="price-preset flex-grow-1" data-min="5000000" data-max="10000000">5 - 10 Tr</button>
                            <button type="button" class="price-preset w-100" data-min="10000000" data-max="">Trên 10 Tr</button>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex flex-column gap-2 mt-3">
                        <button type="submit" class="btn fw-600 py-2 rounded-pill shadow-sm"
                                style="background:#2563eb;color:#fff;font-weight:600;">
                            <i class="fa-solid fa-check me-1"></i> Áp dụng
                        </button>
                        <a href="{{ route('user.products.index') }}" class="btn btn-sm fw-500 py-2 rounded-pill"
                           style="border:1.5px solid #e2e8f0;color:#64748b;font-weight:500;">
                            Xóa bộ lọc
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- ── PRODUCT GRID ── --}}
        <div class="col-lg-9">

            {{-- Active Filter Tags --}}
            @php $hasFilters = $search || $categoryId || $priceMin || $priceMax || $sort !== 'latest'; @endphp
            @if($hasFilters)
                <div class="d-flex flex-wrap gap-2 mb-4 p-3 align-items-center"
                     style="background:#fff;border:1px solid #e2e8f0;border-radius:16px;">
                    <span class="text-muted fw-500" style="font-size:.82rem;font-weight:500;">
                        <i class="fa-solid fa-filter text-primary me-1"></i> Đang lọc:
                    </span>
                    @if($search)
                        <span class="filter-tag" style="background:#eff6ff;color:#2563eb;">
                            "{{ $search }}"
                            <i class="fa-solid fa-xmark fa-xs" onclick="document.querySelector('[name=search]').value=''; document.getElementById('filter-form').submit();" style="cursor:pointer;opacity:.7;"></i>
                        </span>
                    @endif
                    @if($categoryId)
                        @php $catName = $categories->firstWhere('id', $categoryId)?->name ?? ''; @endphp
                        <span class="filter-tag" style="background:#f0fdf4;color:#16a34a;">
                            {{ $catName }}
                            <i class="fa-solid fa-xmark fa-xs" onclick="document.getElementById('cat_all').checked=true;document.getElementById('filter-form').submit();" style="cursor:pointer;opacity:.7;"></i>
                        </span>
                    @endif
                    @if($priceMin || $priceMax)
                        <span class="filter-tag" style="background:#fff7ed;color:#ea580c;">
                            {{ $priceMin ? number_format($priceMin,0,',','.') . 'đ' : '0đ' }} — {{ $priceMax ? number_format($priceMax,0,',','.') . 'đ' : 'Vô tận' }}
                            <i class="fa-solid fa-xmark fa-xs" onclick="document.querySelector('[name=price_min]').value='';document.querySelector('[name=price_max]').value='';document.getElementById('filter-form').submit();" style="cursor:pointer;opacity:.7;"></i>
                        </span>
                    @endif
                </div>
            @endif

            @if($products->isEmpty())
                <div class="text-center py-5 bg-white rounded-4 border" style="border-color:#e2e8f0 !important;">
                    <i class="fa-solid fa-box-open fa-3x text-muted opacity-40 mb-3"></i>
                    <h5 class="fw-bold text-dark">Không tìm thấy sản phẩm nào</h5>
                    <p class="text-muted mb-4">Vui lòng điều chỉnh bộ lọc hoặc thay đổi từ khóa tìm kiếm.</p>
                    <a href="{{ route('user.products.index') }}" class="btn rounded-pill px-5 fw-600" style="background:#2563eb;color:#fff;font-weight:600;">
                        Tải lại tất cả
                    </a>
                </div>
            @else
                @php
                    $wishlistedProductIds = collect();
                    if(Auth::check()) { $wishlistedProductIds = Auth::user()->wishlists->pluck('product_id'); }
                @endphp
                <div class="row g-4">
                    @foreach($products as $product)
                        <div class="col-sm-6 col-xl-4">
                            <div class="card h-100 product-card position-relative border-0">

                                @if($product->created_at >= now()->subDays(7))
                                    <div class="position-absolute z-2" style="top:14px;left:14px;">
                                        <span class="badge rounded-pill px-2 py-1" style="font-size:.7rem;font-weight:700;background:#22c55e;color:#fff;">MỚI</span>
                                    </div>
                                @endif

                                @php $isWishlisted = $wishlistedProductIds->contains($product->id); @endphp
                                <button type="button" class="position-absolute z-2 toggle-wishlist-btn"
                                        style="top:14px;right:14px;" data-id="{{ $product->id }}"
                                        title="{{ $isWishlisted ? 'Bỏ thích' : 'Yêu thích' }}">
                                    <i class="{{ $isWishlisted ? 'fa-solid text-danger' : 'fa-regular text-muted' }} fa-heart fa-sm"></i>
                                </button>

                                <a href="{{ route('user.products.show', $product->id) }}" class="d-block text-decoration-none">
                                    <div class="product-img-wrapper">
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="product-img">
                                        @else
                                            <i class="fa-solid fa-washing-machine fa-4x text-secondary opacity-25"></i>
                                        @endif
                                    </div>
                                </a>

                                <div class="card-body p-3 d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="badge px-2 py-1" style="font-size:.7rem;background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;border-radius:6px;">
                                            {{ $product->category->name ?? 'N/A' }}
                                        </span>
                                        <span class="text-muted" style="font-size:.75rem;font-weight:500;">{{ $product->brand }}</span>
                                    </div>

                                    <h6 class="text-dark mb-2" style="font-weight:700;font-size:.9rem;min-height:2.5rem;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;" title="{{ $product->name }}">
                                        <a href="{{ route('user.products.show', $product->id) }}" class="text-dark text-decoration-none">{{ $product->name }}</a>
                                    </h6>

                                    {{-- Variants color dots --}}
                                    @if($product->variants->isNotEmpty())
                                        <div class="d-flex gap-1 mb-2 flex-wrap align-items-center">
                                            <span class="text-muted me-1" style="font-size:.75rem;">Màu:</span>
                                            @foreach($product->variants->where('stock', '>', 0)->take(4) as $v)
                                                @if($v->color_code)
                                                    <span class="rounded-circle border border-2 shadow-sm d-inline-block"
                                                          title="{{ $v->color_name }}"
                                                          style="width:18px;height:18px;background:{{ $v->color_code }};"></span>
                                                @else
                                                    <span class="badge" style="font-size:.7rem;background:#f1f5f9;color:#334155;border:1px solid #e2e8f0;">{{ $v->color_name }}</span>
                                                @endif
                                            @endforeach
                                            @if($product->variants->where('stock', '>', 0)->count() > 4)
                                                <span class="text-muted ms-1" style="font-size:.75rem;">+{{ $product->variants->where('stock', '>', 0)->count() - 4 }}</span>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="mt-auto pt-3 border-top" style="border-color:#f1f5f9 !important;">
                                        <div class="text-danger mb-3" style="font-size:1.1rem;font-weight:800;letter-spacing:-.5px;">
                                            {{ number_format($product->price, 0, ',', '.') }} đ
                                        </div>
                                        <div class="d-grid gap-2 d-xl-flex">
                                            <a href="{{ route('user.products.show', $product->id) }}"
                                               class="btn btn-sm w-100 rounded-pill fw-600"
                                               style="font-weight:600;border:1.5px solid #2563eb;color:#2563eb;">
                                                Chi Tiết
                                            </a>
                                            <form action="{{ route('cart.add', $product->id) }}" method="POST" class="w-100">
                                                @csrf
                                                <button type="submit" class="btn btn-sm fw-600 w-100 rounded-pill shadow-sm" style="background:#2563eb;color:#fff;font-weight:600;">
                                                    <i class="fa-solid fa-cart-plus fa-sm"></i> Thêm
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination --}}
                @if($products->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $products->onEachSide(1)->links('pagination::bootstrap-5') }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.price-preset').forEach(function (btn) {
        btn.addEventListener('click', function () {
            document.querySelector('[name="price_min"]').value = this.dataset.min;
            document.querySelector('[name="price_max"]').value = this.dataset.max;
            document.querySelectorAll('.price-preset').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
</script>

@endsection
