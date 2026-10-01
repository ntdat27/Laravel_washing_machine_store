<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin - WashingStore</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-light">
    <!-- Thanh Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold tracking-wide" href="{{ route('admin.dashboard') }}">
                <i class="fa-solid fa-shield-halved text-warning me-2"></i>ADMIN PANEL
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active fw-bold text-warning' : '' }}"
                            href="{{ route('admin.dashboard') }}">
                            <i class="fa-solid fa-house"></i> Tổng quan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active fw-bold text-warning' : '' }}"
                            href="{{ route('admin.products.index') }}">
                            <i class="fa-solid fa-box-open"></i> Quản lý Máy Giặt
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active fw-bold text-warning' : '' }}"
                            href="{{ route('admin.categories.index') }}">
                            <i class="fa-solid fa-list"></i> Quản lý Danh Mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active fw-bold text-warning' : '' }}"
                            href="{{ route('admin.users.index') }}">
                            <i class="fa-solid fa-users"></i> Quản lý Người dùng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active fw-bold text-warning' : '' }}"
                            href="{{ route('admin.reports.index') }}">
                            <i class="fa-solid fa-chart-line"></i> Báo Cáo Doanh Thu
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active fw-bold text-warning' : '' }}"
                            href="{{ route('admin.orders.index') }}">
                            <i class="fa-solid fa-file-invoice-dollar"></i> Quản lý Đơn Hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.finance.index') ? 'active fw-bold text-warning' : '' }}"
                            href="{{ route('admin.finance.index') }}">
                            <i class="fa-solid fa-chart-pie"></i> Thống kê tài chính
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.finance.transactions') ? 'active fw-bold text-warning' : '' }}"
                            href="{{ route('admin.finance.transactions') }}">
                            <i class="fa-solid fa-money-check-dollar"></i> Giao dịch thanh toán
                        </a>
                    </li>
                </ul>

                <div class="d-flex align-items-center">
                    <span class="text-light me-3">
                        <i class="fa-solid fa-circle-user"></i> Chào,
                        <strong>{{ auth()->user()->name ?? 'Quản trị viên' }}</strong>
                    </span>
                    <form action="{{ route('logout') }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger btn-sm fw-bold">
                            <i class="fa-solid fa-power-off"></i> Thoát
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Phần Nội Dung Động -->
    <main class="container pb-5">
        @yield('content')
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script tắt tự động thông báo Alert -->
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
    <div id="admin-chat-box" style="position: fixed; bottom: 20px; right: 20px; z-index: 1000;">
        <button id="chat-toggle" class="btn btn-dark shadow">Chat Khách hàng</button>
        <div id="chat-popup" class="card shadow-lg" style="display: none; width: 600px;">
            <div class="card-header bg-dark text-white d-flex justify-content-between">
                <strong>Hỗ trợ trực tuyến</strong>
                <button id="chat-close" class="btn btn-sm btn-light">X</button>
            </div>
            <div class="d-flex" style="height: 400px;">
                <div id="user-list" class="border-end p-2" style="width: 30%; overflow-y: auto;">
                    <div class="p-2 text-center text-muted"><small>Đang tải...</small></div>
                </div>
                <div class="p-2 d-flex flex-column" style="width: 70%;">
                    <div id="chat-messages" class="flex-grow-1" style="overflow-y: auto; padding-bottom: 10px;">
                        <div class="text-center mt-5 text-muted">Chọn một khách hàng để xem tin nhắn</div>
                    </div>
                    <div class="input-group mt-2">
                        <input type="text" id="chat-input" class="form-control" placeholder="Nhập câu trả lời...">
                        <button id="send-btn" class="btn btn-success">Gửi</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let currentUserId = null;
            const toggleBtn = document.getElementById("chat-toggle");
            const chatPopup = document.getElementById("chat-popup");
            const chatMessages = document.getElementById("chat-messages");
            const chatInput = document.getElementById("chat-input");
            const sendBtn = document.getElementById("send-btn");

            toggleBtn.onclick = () => {
                chatPopup.style.display = "block";
                toggleBtn.style.display = "none";
                loadUsers();
            };

            document.getElementById("chat-close").onclick = () => {
                chatPopup.style.display = "none";
                toggleBtn.style.display = "block";
            };

            function loadUsers() {
                fetch("{{ route('admin.chat.users') }}")
                    .then(res => res.json())
                    .then(users => {
                        let html = "";
                        users.forEach(user => {
                            let activeClass = (currentUserId == user.id) ? 'bg-secondary text-white' : 'text-dark';
                            html += `<div class="p-2 border-bottom user-item ${activeClass}" style="cursor: pointer;" onclick="selectUser(${user.id})">
                                ${user.name}
                             </div>`;
                        });
                        document.getElementById("user-list").innerHTML = html || '<div class="p-2 text-muted">Chưa có hội thoại</div>';
                    });
            }

            window.selectUser = function (userId) {
                currentUserId = userId;
                loadUsers();
                loadMessages();
            }

            function loadMessages() {
                if (!currentUserId) return;
                fetch(`/admin/chat/messages/${currentUserId}`)
                    .then(res => res.json())
                    .then(messages => {
                        let html = "";
                        messages.forEach(msg => {
                            let isAdmin = msg.sender_id == "{{ Auth::id() }}";
                            html += `<div class="mb-2" style="text-align: ${isAdmin ? 'right' : 'left'}">
                                <span class="badge ${isAdmin ? 'bg-dark' : 'bg-info'} p-2 text-wrap" style="font-size: 14px; max-width: 80%;">
                                    ${msg.content}
                                </span>
                             </div>`;
                        });
                        chatMessages.innerHTML = html;
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    });
            }

            function sendMessage() {
                let message = chatInput.value.trim();
                if (!message || !currentUserId) return;

                fetch("{{ route('admin.chat.send') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        message: message,
                        user_id: currentUserId
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        chatInput.value = "";
                        loadMessages();
                    })
                    .catch(err => console.error("Lỗi gửi tin:", err));
            }

            sendBtn.onclick = sendMessage;
            chatInput.onkeypress = (e) => { if (e.key === 'Enter') sendMessage(); };

            setInterval(() => {
                if (chatPopup.style.display === "block") {
                    if (currentUserId) loadMessages();
                    loadUsers();
                }
            }, 3000);
        });
    </script>
</body>

</html>