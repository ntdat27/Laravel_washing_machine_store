<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'WashingStore - Siêu Thị Máy Giặt Chính Hãng')</title>
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;1,14..32,400&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary:   #2563eb;
            --primary-d: #1d4ed8;
            --danger:    #ef4444;
            --success:   #22c55e;
            --surface:   #f8faff;
            --text-main: #0f172a;
            --text-muted:#64748b;
            --border:    #e2e8f0;
            --radius-lg: 20px;
            --radius-md: 12px;
            --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md: 0 4px 16px rgba(0,0,0,.08);
            --shadow-lg: 0 12px 40px rgba(0,0,0,.12);
            --transition: all .25s cubic-bezier(.25,.8,.25,1);
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background-color: var(--surface);
            color: var(--text-main);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── NAVBAR ── */
        .site-navbar {
            background: rgba(255,255,255,0.82);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(226,232,240,.7);
            transition: var(--transition);
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .navbar-brand-text {
            font-size: 1.4rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: .45rem;
        }
        .navbar-brand-text:hover { color: var(--primary-d); }
        .navbar-brand-icon {
            width: 36px; height: 36px;
            background: var(--primary);
            border-radius: 10px;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: .9rem;
        }

        .nav-link-custom {
            font-weight: 500;
            font-size: .92rem;
            color: var(--text-muted) !important;
            padding: .45rem .75rem !important;
            border-radius: 8px;
            transition: var(--transition);
            text-decoration: none;
        }
        .nav-link-custom:hover, .nav-link-custom.active {
            color: var(--primary) !important;
            background: #eff6ff;
        }

        /* Cart button */
        .cart-pill {
            display: inline-flex; align-items: center; gap: .5rem;
            background: #eff6ff;
            color: var(--primary);
            border: 1.5px solid #bfdbfe;
            border-radius: 50px;
            padding: .42rem 1.1rem;
            font-weight: 600;
            font-size: .9rem;
            text-decoration: none;
            transition: var(--transition);
        }
        .cart-pill:hover {
            background: var(--primary);
            color: #fff !important;
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37,99,235,.25);
        }
        .cart-count-badge {
            background: var(--danger);
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 50px;
            display: inline-flex; align-items: center; justify-content: center;
            padding: 0 4px;
            border: 2px solid #eff6ff;
            position: absolute;
            top: -6px; right: -8px;
        }
        .cart-pill:hover .cart-count-badge { border-color: var(--primary); }

        /* User dropdown */
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 2px solid var(--border);
            object-fit: cover;
            transition: var(--transition);
        }
        .user-avatar:hover { border-color: var(--primary); }
        .dropdown-menu-modern {
            border: 1px solid var(--border) !important;
            border-radius: var(--radius-md) !important;
            box-shadow: var(--shadow-lg) !important;
            padding: .4rem !important;
            margin-top: 8px !important;
            min-width: 200px;
            animation: dropIn .2s ease;
        }
        @keyframes dropIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .dropdown-item-modern {
            border-radius: 8px;
            padding: .55rem .9rem;
            font-size: .9rem;
            font-weight: 500;
            color: var(--text-main);
            display: flex; align-items: center; gap: .6rem;
            transition: var(--transition);
        }
        .dropdown-item-modern:hover {
            background: #eff6ff;
            color: var(--primary);
        }
        .dropdown-item-modern.danger { color: var(--danger); }
        .dropdown-item-modern.danger:hover { background: #fef2f2; }

        /* Mobile collapse */
        @media (max-width: 991px) {
            .navbar-collapse-custom {
                background: rgba(255,255,255,0.97);
                backdrop-filter: blur(20px);
                border: 1px solid var(--border);
                border-radius: var(--radius-md);
                margin-top: 10px;
                padding: 1rem;
                box-shadow: var(--shadow-lg);
            }
        }

        /* ── MAIN ── */
        .main-content { flex: 1; }

        /* ── PRODUCT CARD (Global shared) ── */
        .product-card {
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            background: #fff;
            transition: var(--transition);
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-lg) !important;
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
        .price-tag { font-size: 1.25rem; letter-spacing: -.5px; }
        .hover-scale { transition: var(--transition); }
        .hover-scale:hover { transform: scale(1.04); }

        /* ── FOOTER ── */
        footer {
            background: #0f172a;
            color: #94a3b8;
            margin-top: auto;
        }
        .footer-brand { font-size: 1.3rem; font-weight: 800; color: #fff; letter-spacing: -.5px; }
        .footer-heading { font-weight: 700; color: #f1f5f9; font-size: .82rem; letter-spacing: 1.2px; text-transform: uppercase; margin-bottom: 1.2rem; }
        .footer-link {
            color: #94a3b8; text-decoration: none;
            font-size: .9rem; display: flex; align-items: center; gap: .5rem;
            padding: .3rem 0; transition: var(--transition);
        }
        .footer-link:hover { color: #fff; transform: translateX(3px); }
        .footer-link .fa-angle-right { font-size: .7rem; opacity: .6; }
        .social-btn {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,.08);
            color: #94a3b8;
            display: inline-flex; align-items: center; justify-content: center;
            text-decoration: none;
            transition: var(--transition);
            font-size: .9rem;
        }
        .social-btn:hover { background: var(--primary); color: #fff; transform: translateY(-3px); }
        .footer-divider { border-color: rgba(255,255,255,.08); }
        .footer-contact-item {
            display: flex; gap: .75rem; align-items: flex-start;
            font-size: .9rem; margin-bottom: .85rem;
        }
        .footer-contact-item .icon { color: var(--primary); margin-top: 2px; flex-shrink: 0; }

        /* ── WISHLIST BTN (global) ── */
        .toggle-wishlist-btn {
            background: rgba(255,255,255,.9);
            backdrop-filter: blur(8px);
            border: 1px solid var(--border);
            border-radius: 50%;
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: var(--transition);
        }
        .toggle-wishlist-btn:hover {
            background: #fff;
            box-shadow: 0 4px 12px rgba(239,68,68,.15);
            transform: scale(1.1);
        }

        /* ── TOGGLER ── */
        .navbar-toggler { border: 1px solid var(--border) !important; border-radius: 10px !important; }
        .navbar-toggler:focus { box-shadow: none !important; }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='%232563eb' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
        }
    </style>
</head>

<body>

    <!-- ──────── NAVBAR ──────── -->
    <header class="site-navbar py-2 shadow-sm">
        <div class="container">
            <nav class="navbar navbar-expand-lg p-0">
                <!-- Brand -->
                <a class="navbar-brand-text me-4" href="{{ route('welcome') }}">
                    <span class="navbar-brand-icon"><i class="fa-solid fa-shirt"></i></span>
                    WashingStore
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse navbar-collapse-custom" id="mainNav">
                    <!-- Left nav -->
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-1">
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('welcome') ? 'active' : '' }}" href="{{ route('welcome') }}">
                                <i class="fa-solid fa-house fa-sm me-1"></i> Trang chủ
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link-custom {{ request()->routeIs('user.products.*') ? 'active' : '' }}" href="{{ route('user.products.index') }}">
                                <i class="fa-solid fa-washing-machine fa-sm me-1"></i> Sản phẩm
                            </a>
                        </li>
                    </ul>

                    <hr class="d-lg-none my-2" style="border-color: var(--border);">

                    <!-- Right nav -->
                    <div class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center gap-2 gap-lg-3">

                        <!-- Cart -->
                        <a href="{{ route('cart.index') }}" class="cart-pill position-relative">
                            <i class="fa-solid fa-cart-shopping"></i>
                            <span>Giỏ hàng</span>
                            @php $cartCount = count(session('cart', [])); @endphp
                            @if($cartCount > 0)
                                <span class="cart-count-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                            @endif
                        </a>

                        <hr class="d-lg-none my-1 w-100" style="border-color: var(--border);">

                        <!-- Auth -->
                        @auth
                            <div class="nav-item dropdown">
                                <a class="nav-link-custom d-flex align-items-center gap-2 px-2 py-1" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=2563eb&color=fff&bold=true&size=80" alt="Avatar" class="user-avatar">
                                    <div class="d-none d-lg-block text-start" style="line-height: 1.3;">
                                        <span class="d-block" style="font-size:.7rem;color:var(--text-muted);">Xin chào,</span>
                                        <span class="fw-700 text-dark" style="font-size:.9rem;font-weight:700;">{{ Auth::user()->name }}</span>
                                    </div>
                                    <span class="d-lg-none fw-600 text-dark ms-1" style="font-weight:600;">{{ Auth::user()->name }}</span>
                                    <i class="fa-solid fa-chevron-down fa-xs text-muted d-none d-lg-inline"></i>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-modern dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li>
                                        <a class="dropdown-item-modern {{ request()->routeIs('user.orders.*') ? 'text-primary' : '' }}" href="{{ route('user.orders.index') }}">
                                            <i class="fa-solid fa-box-open fa-fw text-muted"></i> Đơn của tôi
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item-modern {{ request()->routeIs('user.wishlist.*') ? 'text-primary' : '' }}" href="{{ route('user.wishlist.index') }}">
                                            <i class="fa-solid fa-heart fa-fw text-danger"></i> Danh sách yêu thích
                                        </a>
                                    </li>
                                    @if(Auth::user()->role === 'admin')
                                    <li>
                                        <a class="dropdown-item-modern" href="{{ route('admin.dashboard') }}">
                                            <i class="fa-solid fa-gauge fa-fw text-muted"></i> Vào trang Admin
                                        </a>
                                    </li>
                                    @endif
                                    <li><hr class="dropdown-divider my-1" style="border-color:var(--border);"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item-modern danger w-100 text-start border-0 bg-transparent">
                                                <i class="fa-solid fa-right-from-bracket fa-fw"></i> Đăng xuất
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <div class="d-flex flex-column flex-lg-row gap-2">
                                <a class="nav-link-custom px-3" href="{{ route('login') }}">Đăng nhập</a>
                                <a class="btn btn-sm fw-600 px-4 rounded-pill shadow-sm" style="background:var(--primary);color:#fff;font-weight:600;" href="{{ route('register') }}">Đăng ký ngay</a>
                            </div>
                        @endauth
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- ──────── FOOTER ──────── -->
    <footer class="pt-5 pb-4 mt-auto">
        <div class="container">
            <div class="row gy-5">
                <!-- Cột Thương hiệu -->
                <div class="col-lg-4 col-md-12">
                    <div class="footer-brand d-flex align-items-center gap-2 mb-3">
                        <div class="navbar-brand-icon" style="background:var(--primary);">
                            <i class="fa-solid fa-shirt fa-sm"></i>
                        </div>
                        WashingStore
                    </div>
                    <p style="font-size:.9rem;line-height:1.75;" class="mb-4">
                        Hệ thống phân phối máy giặt chính hãng hàng đầu Việt Nam. Sản phẩm chất lượng cao, bảo hành tận tâm và giá cả cạnh tranh nhất thị trường.
                    </p>
                    <div class="d-flex gap-2">
                        <a href="#" class="social-btn"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="social-btn"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Cột Chính sách -->
                <div class="col-lg-2 col-md-4">
                    <div class="footer-heading">Hỗ Trợ</div>
                    <a href="#" class="footer-link"><i class="fa-solid fa-angle-right"></i> Trang chủ</a>
                    <a href="{{ route('user.products.index') }}" class="footer-link"><i class="fa-solid fa-angle-right"></i> Sản phẩm</a>
                    <a href="#" class="footer-link"><i class="fa-solid fa-angle-right"></i> Khuyến mãi</a>
                    <a href="#" class="footer-link"><i class="fa-solid fa-angle-right"></i> Bảo hành</a>
                    <a href="#" class="footer-link"><i class="fa-solid fa-angle-right"></i> Chính sách đổi trả</a>
                </div>

                <!-- Cột Dịch vụ -->
                <div class="col-lg-2 col-md-4">
                    <div class="footer-heading">Dịch Vụ</div>
                    <a href="#" class="footer-link"><i class="fa-solid fa-angle-right"></i> Giao hàng toàn quốc</a>
                    <a href="#" class="footer-link"><i class="fa-solid fa-angle-right"></i> Lắp đặt tại nhà</a>
                    <a href="#" class="footer-link"><i class="fa-solid fa-angle-right"></i> Tư vấn miễn phí</a>
                    <a href="#" class="footer-link"><i class="fa-solid fa-angle-right"></i> Thu hồi cũ</a>
                </div>

                <!-- Cột Liên hệ -->
                <div class="col-lg-4 col-md-4">
                    <div class="footer-heading">Thông Tin Liên Hệ</div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-location-dot icon"></i>
                        <span>123 Đường Công Nghệ, Quận Thủ Đức, TP. Hồ Chí Minh</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-phone icon"></i>
                        <span class="fw-600" style="font-weight:600;color:#e2e8f0;">1900 1008</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-envelope icon"></i>
                        <span>hotro@washingstore.vn</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fa-solid fa-clock icon"></i>
                        <span>Thứ 2 – Chủ Nhật: 8:00 – 21:00</span>
                    </div>
                </div>
            </div>

            <hr class="footer-divider my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 text-center">
                <p class="mb-0" style="font-size:.85rem;">&copy; {{ date('Y') }} WashingStore. Bản quyền thuộc về Nhóm Phát Triển.</p>
                <div class="d-flex gap-3" style="font-size:.82rem;">
                    <a href="#" class="footer-link p-0">Điều khoản sử dụng</a>
                    <a href="#" class="footer-link p-0">Chính sách bảo mật</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- SweetAlert từ session -->
    @if(session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Tuyệt vời!",
                    text: "{!! session('success') !!}",
                    icon: "success",
                    confirmButtonColor: "#2563eb",
                    confirmButtonText: "OK",
                    timer: 3500
                });
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    title: "Có lỗi xảy ra!",
                    text: "{!! session('error') !!}",
                    icon: "error",
                    confirmButtonColor: "#ef4444",
                    confirmButtonText: "Đóng"
                });
            });
        </script>
    @endif

    @auth
        <!-- Chat Widget -->
        <div id="chat-box" style="position: fixed; bottom: 24px; right: 24px; z-index: 1050;">
            <button id="chat-toggle"
                class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center"
                style="width:58px;height:58px;background:var(--primary);color:#fff;border:none;font-size:1.3rem;transition:transform .2s;">
                <i class="fa-solid fa-comments"></i>
            </button>
            <div id="chat-popup" class="card shadow-lg border-0" style="display:none;border-radius:18px;width:340px;overflow:hidden;position:absolute;bottom:70px;right:0;">
                <div class="card-header text-white d-flex justify-content-between align-items-center py-3 border-0"
                     style="background:var(--primary);">
                    <span class="fw-bold"><i class="fa-solid fa-headset me-2"></i>Hỗ trợ khách hàng</span>
                    <button id="chat-close" class="btn btn-sm btn-link text-white p-0 shadow-none"><i class="fa-solid fa-xmark fs-5"></i></button>
                </div>
                <div id="chat-messages" class="card-body" style="height:320px;overflow-y:auto;background:#f8faff;padding:1rem;">
                    <div class="text-center text-muted mt-3"><small>Bắt đầu cuộc trò chuyện với Admin</small></div>
                </div>
                <div class="card-footer bg-white border-top py-2 px-3">
                    <div class="input-group">
                        <input type="text" id="chat-input" class="form-control rounded-pill border me-2" placeholder="Nhập tin nhắn..." autocomplete="off" style="font-size:.9rem;">
                        <button id="send-btn" class="btn rounded-circle d-flex align-items-center justify-content-center"
                                style="width:38px;height:38px;background:var(--primary);color:#fff;border:none;flex-shrink:0;">
                            <i class="fa-solid fa-paper-plane fa-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const toggleBtn   = document.getElementById("chat-toggle");
                const chatPopup   = document.getElementById("chat-popup");
                const closeBtn    = document.getElementById("chat-close");
                const sendBtn     = document.getElementById("send-btn");
                const input       = document.getElementById("chat-input");
                const chatBox     = document.getElementById("chat-messages");

                if (!toggleBtn) return;

                toggleBtn.addEventListener('mouseenter', () => toggleBtn.style.transform = 'scale(1.08)');
                toggleBtn.addEventListener('mouseleave', () => toggleBtn.style.transform = 'scale(1)');

                toggleBtn.onclick = () => { chatPopup.style.display = "block"; toggleBtn.style.display = "none"; loadMessages(); };
                closeBtn.onclick  = () => { chatPopup.style.display = "none";  toggleBtn.style.display = "flex"; };

                function loadMessages() {
                    fetch("{{ route('chat.messages') }}")
                        .then(res => res.json())
                        .then(messages => {
                            let html = "";
                            if (!messages.length) html = "<div class='text-center text-muted mt-3'><small>Bắt đầu cuộc trò chuyện với Admin</small></div>";
                            messages.forEach(msg => {
                                const isMe = msg.sender_id == "{{ Auth::id() }}";
                                html += `<div class="mb-2 d-flex flex-column" style="align-items:${isMe ? 'flex-end' : 'flex-start'}">
                                    <span class="px-3 py-2 fw-normal" style="font-size:.88rem;max-width:82%;border-radius:16px;${isMe ? 'border-bottom-right-radius:4px;background:#2563eb;color:#fff;' : 'border-bottom-left-radius:4px;background:#fff;color:#0f172a;border:1px solid #e2e8f0;'}">
                                        ${msg.content}
                                    </span>
                                </div>`;
                            });
                            chatBox.innerHTML = html;
                            chatBox.scrollTop = chatBox.scrollHeight;
                        })
                        .catch(err => console.error("Lỗi tải tin nhắn:", err));
                }

                function sendMessage() {
                    let message = input.value.trim();
                    if (!message) return;
                    input.disabled = true; sendBtn.disabled = true;
                    fetch("{{ route('chat.send') }}", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            "Content-Type": "application/json",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({ message })
                    }).then(res => res.json()).then(() => {
                        input.value = ""; input.disabled = false; sendBtn.disabled = false; input.focus(); loadMessages();
                    }).catch(() => { input.disabled = false; sendBtn.disabled = false; });
                }

                sendBtn.onclick = sendMessage;
                input.addEventListener("keypress", e => { if (e.key === "Enter") sendMessage(); });
                setInterval(() => { if (chatPopup.style.display === "block") loadMessages(); }, 3000);
            });
        </script>
    @endauth

    <!-- Wishlist AJAX (Global) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-wishlist-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault(); e.stopPropagation();
                    const productId = this.getAttribute('data-id');
                    const icon = this.querySelector('i');
                    fetch("{{ route('user.wishlist.toggle') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ product_id: productId })
                    }).then(res => res.json()).then(data => {
                        if (data.status === 'added') {
                            icon.classList.remove('fa-regular', 'text-muted');
                            icon.classList.add('fa-solid', 'text-danger');
                            btn.title = 'Bỏ thích';
                        } else if (data.status === 'removed') {
                            icon.classList.remove('fa-solid', 'text-danger');
                            icon.classList.add('fa-regular', 'text-muted');
                            btn.title = 'Yêu thích';
                        } else if (data.message === 'Unauthenticated.') {
                            window.location.href = "{{ route('login') }}";
                        }
                    }).catch(err => console.error(err));
                });
            });
        });
    </script>

</body>
</html>