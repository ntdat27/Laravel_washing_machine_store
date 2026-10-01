<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Máy Giặt - Laravel CRUD</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-header text-white fw-bold text-decoration-none fs-4"
                href="{{ route('admin.products.index') }}">
                <i class="fa-solid fa-washing-machine"></i> Quản Lý Máy Giặt
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link text-white {{ request()->routeIs('products.*') ? 'active fw-bold border-bottom' : '' }}"
                    href="{{ route('admin.products.index') }}">
                    <i class="fa-solid fa-box"></i> Quản lý Sản Phẩm
                </a>
                <a class="nav-link text-white ms-3 {{ request()->routeIs('categories.*') ? 'active fw-bold border-bottom' : '' }}"
                    href="{{ route('categories.index') }}">
                    <i class="fa-solid fa-list"></i> Quản lý Danh Mục
                </a>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>