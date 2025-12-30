<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\TrustProxies;

// 🧩 tambahkan import AdminMiddleware di sini
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\Authenticate;
use Illuminate\Auth\Middleware\EnsureEmailIsVerified;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
 ->withMiddleware(function (Middleware $middleware) {

    // ✅ TRUST PROXY (WAJIB UNTUK RAILWAY)
    $middleware->trustProxies(at: '*');

    // ✅ Alias middleware kamu
    $middleware->alias([
        'auth' => Authenticate::class,
        'verified' => EnsureEmailIsVerified::class,
        'admin' => AdminMiddleware::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withCommands([
    App\Console\Commands\GenerateRealtimeTagihan::class,
])
->withCommands([
    App\Console\Commands\AutoGenerateTagihan::class,
])

    ->create();
