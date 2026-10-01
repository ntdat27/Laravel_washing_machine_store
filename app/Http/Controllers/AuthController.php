<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Tạo tài khoản và kích hoạt email ngay lập tức để tránh lỗi timeout/chặn SMTP trên cloud
        $user = User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => 'user',
            'email_verified_at' => now(),
        ]);

        // Thử gửi notification email xác nhận (nếu hệ thống mail được cấu hình)
        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::warning('[Mail] Không thể gửi email chào mừng/xác thực: ' . $e->getMessage());
        }

        Auth::login($user);

        return redirect()->route('welcome')->with('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với WashingStore.');
    }

    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Tự động kích hoạt email cho các tài khoản cũ chưa kịp xác thực để không bị kẹt trang
            if (!Auth::user()->hasVerifiedEmail()) {
                Auth::user()->forceFill(['email_verified_at' => now()])->save();
            }

            if (Auth::user()->role === 'admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->route('welcome');
        }

        return back()->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}