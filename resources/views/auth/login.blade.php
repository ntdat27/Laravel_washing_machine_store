<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - WashingStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="text-center mb-4">
                    <a href="{{ route('welcome') }}" class="text-decoration-none">
                        <h2 class="fw-bold text-primary"><i class="fa-solid fa-shirt me-2"></i>WashingStore</h2>
                    </a>
                </div>

                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-dark">Đăng Nhập</h4>
                            <p class="text-muted small">Chào mừng bạn quay trở lại!</p>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger border-0 shadow-sm rounded-3 py-2">
                                <i class="fa-solid fa-circle-exclamation me-2"></i>{{ $errors->first() }}
                            </div>
                        @endif

                        <form action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold text-secondary small">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fa-solid fa-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control border-start-0 bg-light"
                                        placeholder="Nhập địa chỉ email" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-bold text-secondary small">Mật khẩu</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fa-solid fa-lock text-muted"></i></span>
                                    <input type="password" name="password" class="form-control border-start-0 bg-light"
                                        placeholder="Nhập mật khẩu" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-pill shadow-sm">
                                <i class="fa-solid fa-right-to-bracket me-2"></i>Đăng Nhập
                            </button>
                        </form>

                        <div class="mt-4 text-center">
                            <span class="text-muted small">Chưa có tài khoản?</span>
                            <a href="{{ route('register') }}" class="text-decoration-none fw-bold ms-1">Đăng ký ngay</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>