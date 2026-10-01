<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminConfig = config('seeding.admin');
        $email = $adminConfig['email'] ?? null;
        $password = $adminConfig['password'] ?? null;
        $name = $adminConfig['name'] ?? 'Administrator';

        // 1. Kiểm tra cấu hình có được cung cấp hay không
        if (empty($email) || empty($password)) {
            $this->command?->warn('Bỏ qua tạo Admin: SEED_ADMIN_EMAIL hoặc SEED_ADMIN_PASSWORD chưa được cấu hình.');
            return;
        }

        // 2. Validate email hợp lệ
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->command?->error("Email admin không đúng định dạng: {$email}");
            throw new InvalidArgumentException("Email admin không đúng định dạng: {$email}");
        }

        // 3. Kiểm tra độ dài mật khẩu >= 12 ký tự theo chuẩn bảo mật
        if (strlen($password) < 12) {
            $this->command?->error('Mật khẩu admin không đủ an toàn: Yêu cầu tối thiểu 12 ký tự.');
            throw new InvalidArgumentException('Mật khẩu admin phải có độ dài tối thiểu 12 ký tự.');
        }

        // 4. Tạo hoặc cập nhật tài khoản Admin
        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $this->command?->info("Khởi tạo tài khoản Admin thành công cho email: {$email}");
    }
}