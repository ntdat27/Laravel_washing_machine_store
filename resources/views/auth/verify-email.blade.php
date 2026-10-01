<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực Email - WashingStore</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light d-flex align-items-center vh-100">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="text-center mb-4">
                    <a href="{{ route('welcome') }}" class="text-decoration-none">
                        <h2 class="fw-bold text-primary"><i class="fa-solid fa-shirt me-2"></i>WashingStore</h2>
                    </a>
                </div>

                <div class="card shadow border-0 rounded-4">
                    <div class="card-header bg-warning text-dark fw-bold text-center py-3 border-0 rounded-top-4">
                        <h5 class="mb-0"><i class="fa-solid fa-envelope-circle-check me-2"></i>Yêu Cầu Xác Thực Email
                        </h5>
                    </div>
                    <div class="card-body text-center p-5">
                        <div class="mb-4 text-success">
                            <i class="fa-solid fa-paper-plane fa-3x opacity-75"></i>
                        </div>
                        <p class="fs-5 text-dark fw-bold mb-2">Đăng ký thành công!</p>
                        <p class="text-muted mb-4 small">Vui lòng kiểm tra hộp thư email của bạn (bao gồm cả thư mục
                            Spam) và click vào link xác nhận để kích hoạt tài khoản hoàn toàn.</p>

                        @if (session('message'))
                            <div class="alert alert-success border-0 shadow-sm rounded-3 py-2 small fw-bold">
                                <i class="fa-solid fa-check-circle me-1"></i> Email xác nhận mới đã được gửi!
                            </div>
                        @endif

                        <hr class="my-4 border-light">

                        <form method="POST" action="{{ route('verification.instant') }}" class="mb-3">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm w-100">
                                <i class="fa-solid fa-bolt me-2"></i> Kích hoạt tài khoản ngay (Không cần đợi email)
                            </button>
                        </form>

                        <p class="text-muted small mb-2">Hoặc gửi lại email qua hòm thư:</p>
                        <form method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="fa-solid fa-rotate-right me-2"></i> Gửi lại email xác nhận
                            </button>
                        </form>

                        <div class="mt-4">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-link text-muted text-decoration-none small">
                                    <i class="fa-solid fa-arrow-left me-1"></i> Đăng xuất và quay lại
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>