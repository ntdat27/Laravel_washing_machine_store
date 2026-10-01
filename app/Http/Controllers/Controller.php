<?php

namespace App\Http\Controllers;

abstract class Controller
{
    /**
     * Kiểm tra nhanh xem cổng SMTP có kết nối được không trong tối đa 1.0 giây.
     * Ngăn chặn tuyệt đối việc treo kết nối 30 giây gây lỗi 502 Bad Gateway trên Cloud (Render).
     */
    protected function canSendSmtp(): bool
    {
        $host = config('mail.mailers.smtp.host');
        $port = (int) config('mail.mailers.smtp.port', 587);

        if (empty($host) || config('mail.default') !== 'smtp') {
            return false;
        }

        $connection = @fsockopen($host, $port, $errno, $errstr, 1.0);
        if (is_resource($connection)) {
            fclose($connection);
            return true;
        }

        return false;
    }
}
