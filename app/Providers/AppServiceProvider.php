<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\Pengaduan;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Paksa HTTPS di production
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // ✅ PENTING: percaya proxy Railway
        Request::setTrustedProxies(
            ['*'],
            Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_X_FORWARDED_PROTO
        );

        // Badge jumlah pengaduan
        view()->composer('*', function ($view) {
            $jumlahPengaduanBaru = Pengaduan::where('status', 'baru')->count();
            $view->with('jumlahPengaduanBaru', $jumlahPengaduanBaru);
        });
    }
}
