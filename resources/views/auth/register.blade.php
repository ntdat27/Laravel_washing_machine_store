<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký Tài Khoản - WashingStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light d-flex align-items-center min-vh-100 py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <a href="{{ route('welcome') }}" class="text-decoration-none">
                        <h2 class="fw-bold text-primary"><i class="fa-solid fa-shirt me-2"></i>WashingStore</h2>
                    </a>
                </div>

                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-dark">Tạo Tài Khoản Mới</h4>
                            <p class="text-muted small">Tham gia mua sắm cùng chúng tôi</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2">
                                <ul class="mb-0 ps-3 small">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('register') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small">Họ và Tên</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fa-solid fa-user text-muted"></i></span>
                                    <input type="text" name="name" class="form-control border-start-0 bg-light"
                                        value="{{ old('name') }}" placeholder="VD: Nguyễn Văn A" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fa-solid fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 bg-light"
                                        value="{{ old('email') }}" placeholder="VD: email@example.com" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small">Mật khẩu (Tối thiểu 6 ký
                                    tự)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fa-solid fa-lock text-muted"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0 bg-light"
                                        placeholder="Tạo mật khẩu" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary small">Xác nhận mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fa-solid fa-check-double text-muted"></i></span>
                                    <input type="password" name="password_confirmation"
                                        class="form-control border-start-0 bg-light" placeholder="Nhập lại mật khẩu"
                                        required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                                <i class="fa-solid fa-user-plus me-2"></i>Đăng Ký
                            </button>
                        </form>

                        <div class="mt-4 text-center">
                            <span class="text-muted small">Đã có tài khoản?</span>
                            <a href="{{ route('login') }}" class="text-decoration-none fw-bold ms-1">Đăng nhập ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>