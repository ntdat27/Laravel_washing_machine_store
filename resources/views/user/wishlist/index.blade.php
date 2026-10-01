@extends('user.layout')
@section('title', 'Sản phẩm yêu thích — WashingStore')

@section('content')
<div class="container my-5">
    <div class="mb-4 d-flex justify-content-between align-items-end">
        <div>
            <h2 class="fw-bold text-dark"><i class="fa-solid fa-heart me-2 text-danger"></i>Sản Phẩm Yêu Thích</h2>
            <p class="text-muted mb-0">Lưu lại những sản phẩm bạn quan tâm để mua sau.</p>
        </div>
        <a href="{{ route('welcome') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-sm">
            Tiếp tục mua sắm
        </a>
    </div>

    @if($wishlists->isEmpty())
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 bg-white">
            <div class="card-body py-5">
                <i class="fa-regular fa-heart fa-5x text-muted mb-4 opacity-50"></i>
                <h4 class="fw-bold text-dark">Bạn chưa có sản phẩm yêu thích nào!</h4>
                <p class="text-muted mb-4 fs-5">Hãy dạo quanh cửa hàng và "thả tim" những sản phẩm ưng ý nhé.</p>
                <a href="{{ route('welcome') }}" class="btn btn-danger btn-lg px-5 fw-bold rounded-pill shadow-sm hover-scale">
                    Khám Phá Ngay
                </a>
            </div>
        </div>
    @else
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($wishlists as $wishlist)
                @php $p = $wishlist->product; @endphp
                @if($p)
                <div class="col" id="wishlist-item-{{ $p->id }}">
                    <div class="card h-100 border-0 shadow-sm rounded-4 product-card position-relative overflow-hidden">
                        {{-- Nút gỡ khỏi wishlist --}}
                        <button type="button" class="btn btn-light rounded-circle shadow-sm position-absolute btn-remove-wishlist"
                                style="top: 10px; right: 10px; width: 35px; height: 35px; z-index: 10;"
                                data-id="{{ $p->id }}" title="Bỏ thích">
                            <i class="fa-solid fa-xmark text-danger"></i>
                        </button>

                        <a href="{{ route('user.products.show', $p->id) }}" class="text-decoration-none">
                            <div class="position-relative p-3">
                                @if($p->image)
                                    <img src="{{ asset('storage/' . $p->image) }}" class="card-img-top rounded-3" alt="{{ $p->name }}" style="height: 200px; object-fit: contain;">
                                @else
                                    <div class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fa-solid fa-image text-muted fa-3x"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="card-body d-flex flex-column pt-0">
                                <p class="text-muted small mb-1">{{ $p->category->name ?? 'Khác' }}</p>
                                <h6 class="card-title fw-bold text-dark mb-2 text-truncate">{{ $p->name }}</h6>
                                <div class="mt-auto">
                                    <div class="text-danger fw-bold fs-5 mb-2">{{ number_format($p->price, 0, ',', '.') }} đ</div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
                @endif
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-5">
            {{ $wishlists->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

<style>
    .product-card { transition: all 0.3s ease; }
    .product-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; border-color: rgba(13,110,253,0.3) !important; }
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: scale(1.05); }
    .btn-remove-wishlist { transition: all 0.2s; }
    .btn-remove-wishlist:hover { background-color: #dc3545 !important; color: white !important; }
    .btn-remove-wishlist:hover i { color: white !important; }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const removeBtns = document.querySelectorAll('.btn-remove-wishlist');
    
    removeBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const productId = this.getAttribute('data-id');
            const cardItem = document.getElementById('wishlist-item-' + productId);

            fetch('{{ route('user.wishlist.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ product_id: productId })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'removed') {
                    cardItem.style.transition = "opacity 0.3s ease, transform 0.3s ease";
                    cardItem.style.opacity = '0';
                    cardItem.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        cardItem.remove();
                        // Nếu giỏ trống thì reload trang
                        if (document.querySelectorAll('.btn-remove-wishlist').length === 0) {
                            window.location.reload();
                        }
                    }, 300);
                    
                    const Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true
                    });
                    Toast.fire({ icon: 'success', title: data.message });
                } else if(data.message === 'Unauthenticated.') {
                    window.location.href = '{{ route('login') }}';
                }
            })
            .catch(err => console.error(err));
        });
    });
});
</script>
@endsection
