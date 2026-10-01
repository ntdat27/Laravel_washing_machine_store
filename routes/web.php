<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\User\OrderController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\User\MomoController;
use App\Http\Controllers\User\ReviewController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\FinanceController;


// ==========================================
// 1. ROUTE CÔNG KHAI (Ai cũng xem được)
// ==========================================
Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

// Đăng ký, Đăng nhập, Đăng xuất
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->name('logout');


// ==========================================
// 2. ROUTE XÁC THỰC EMAIL
// ==========================================
Route::get('/email/verify', function () {
    if (auth()->check()) {
        if (!auth()->user()->hasVerifiedEmail()) {
            auth()->user()->forceFill(['email_verified_at' => now()])->save();
        }
        return redirect()->route('welcome')->with('success', 'Tài khoản của bạn đã được kích hoạt thành công!');
    }
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('welcome');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verify-instant', function (Request $request) {
    $request->user()->forceFill(['email_verified_at' => now()])->save();
    return redirect()->route('welcome')->with('success', 'Tài khoản đã được kích hoạt thành công!');
})->middleware('auth')->name('verification.instant');

Route::post('/email/verification-notification', function (Request $request) {
    try {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Email xác nhận mới đã được gửi!');
    } catch (\Throwable $e) {
        \Illuminate\Support\Facades\Log::warning('Gửi lại email xác thực thất bại: ' . $e->getMessage());
        return back()->with('error', 'Hệ thống gửi thư đang bận hoặc bị giới hạn mạng. Vui lòng thử lại sau.');
    }
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// ==========================================
// 3. WEBHOOK / CALLBACK KHÔNG YÊU CẦU ĐĂNG NHẬP
// (GHN, MoMo gọi server-to-server tới đây)
// ==========================================
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');


// ==========================================
// 4. ROUTE NGƯỜI DÙNG BÌNH THƯỜNG (Bắt buộc đăng nhập & xác thực)
// ==========================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Xem danh sách và chi tiết sản phẩm
    Route::get('/products', [ProductController::class, 'index_normal'])->name('user.products.index');
    Route::get('/products/{product}', [ProductController::class, 'show_normal'])->name('user.products.show');

    // Lịch sử Đơn hàng
    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('user.orders.index');
    Route::delete('/my-orders/{order}/cancel', [OrderController::class, 'cancel'])->name('user.orders.cancel');

    // Quản lý Giỏ hàng
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{product}', [CartController::class, 'remove'])->name('cart.remove');

    // Đặt hàng & Thanh toán COD (MoMo xử lý riêng)
    Route::match(['get', 'post'], '/checkout', [CartController::class, 'checkout'])->name('checkout.index');

    // MoMo — Khởi động thanh toán từ trang Checkout (đơn đã được tạo)
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('user.orders.momo.start');
    // MoMo — Thanh toán lại đơn cũ chưa trả tiền
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('user.orders.momo.pay');

    // Đánh giá sản phẩm
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Danh sách yêu thích (Wishlist)
    Route::get('/wishlist', [\App\Http\Controllers\User\WishlistController::class, 'index'])->name('user.wishlist.index');
    Route::post('/wishlist/toggle', [\App\Http\Controllers\User\WishlistController::class, 'toggle'])->name('user.wishlist.toggle');

    // Mã giảm giá (Coupon)
    Route::post('/cart/coupon/apply', [CartController::class, 'applyCoupon'])->name('cart.coupon.apply');
    Route::post('/cart/coupon/remove', [CartController::class, 'removeCoupon'])->name('cart.coupon.remove');

    // User Chat
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');

    // Nhóm Route gọi API của Giao Hàng Nhanh
    Route::prefix('locations')->name('locations.')->group(function () {
        Route::get('/provinces', [OrderController::class, 'getProvinces'])->name('provinces');
        Route::get('/districts/{provinceId}', [OrderController::class, 'getDistricts'])->name('districts');
        Route::get('/wards/{districtId}', [OrderController::class, 'getWards'])->name('wards');
        Route::post('/calculate-fee', [OrderController::class, 'getShippingFee'])->name('fee');
    });
});


// ==========================================
// 5. ROUTE ADMIN (Bắt buộc đăng nhập + Là Admin)
// ==========================================
Route::middleware(['auth', 'admin', 'verified'])->group(function () {
    // Route quản lý Đơn hàng
    Route::resource('/admin/orders', AdminOrderController::class, ['as' => 'admin']);
    // Báo cáo
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/charts', [ReportController::class, 'charts'])->name('admin.reports.charts');
    
    // Finance
    Route::get('/admin/finance', [FinanceController::class, 'index'])->name('admin.finance.index');
    Route::get('/admin/finance/transactions', [FinanceController::class, 'transactions'])->name('admin.finance.transactions');
    Route::patch('/admin/finance/{order}/status', [FinanceController::class, 'updateStatus'])->name('admin.finance.update-status');
    
    // Route quản lý người dùng
    Route::resource('/admin/users', AdminUserController::class, ['as' => 'admin']);
    // Admin Chat
    Route::get('/admin/chat/users', [AdminChatController::class, 'getUsers'])->name('admin.chat.users');
    Route::get('/admin/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('admin.chat.messages');
    Route::post('/admin/chat/send', [AdminChatController::class, 'send'])->name('admin.chat.send');

    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    Route::resource('/admin/products', ProductController::class, ['as' => 'admin']);

    Route::resource('/admin/categories', CategoryController::class, ['as' => 'admin']);
});