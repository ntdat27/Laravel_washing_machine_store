<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Trị Hệ Thống - WashingStore</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <!-- Thanh Menu điều hướng chung -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-shirt"></i> WashingStore Admin
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <div class="navbar-nav me-auto">
                    <a class="nav-link text-white {{ request()->routeIs('admin.products.*') ? 'active fw-bold border-bottom' : '' }}"
                        href="{{ route('admin.products.index') }}">
                        <i class="fa-solid fa-box"></i> Sản Phẩm
                    </a>
                    <a class="nav-link text-white ms-lg-3 {{ request()->routeIs('admin.categories.*') ? 'active fw-bold border-bottom' : '' }}"
                        href="{{ route('admin.categories.index') }}">
                        <i class="fa-solid fa-list"></i> Danh Mục
                    </a>
                </div>

                <div class="navbar-nav ms-auto align-items-center">
                    <span class="nav-item text-white me-3">
                        Xin chào, <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script tự động tắt Alert sau 3 giây -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                let alert = document.querySelector('.alert-success');
                if (alert) {
                    let bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 3000);
        });
    </script>
</body>

</html>