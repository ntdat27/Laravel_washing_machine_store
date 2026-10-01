<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // GIỮ NGUYÊN ALIAS ADMIN CỦA BẠN
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);

        // THÊM ĐOẠN NÀY ĐỂ BỎ QUA KIỂM TRA CSRF CHO MOMO VÀ GHN
        $middleware->validateCsrfTokens(except: [
            '/payment/momo/ipn',
            '/ghn/webhook'
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();