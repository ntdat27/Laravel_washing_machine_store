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

        // Tạo tài khoản mới (chưa xác thực email để yêu cầu người dùng xác nhận)
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'user',
        ]);

        // Kiểm tra nhanh kết nối SMTP trong 1 giây để tránh bị treo 30 giây gây lỗi 502 trên Render
        if ($this->canSendSmtp()) {
            try {
                event(new Registered($user));
            } catch (\Throwable $e) {
                Log::warning('[Mail] Không thể gửi email xác thực: ' . $e->getMessage());
            }
        } else {
            Log::info('[Mail] Mạng Render chặn cổng SMTP 587. Bỏ qua gửi email đồng bộ để tránh lỗi 502 Bad Gateway.');
        }

        // Đăng nhập tạm để đưa vào trang chờ xác thực email
        Auth::login($user);

        return redirect()->route('verification.notice');
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

            // Nếu tài khoản chưa xác thực email, bắt buộc chuyển hướng tới trang xác thực
            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice');
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