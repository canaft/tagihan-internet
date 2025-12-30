<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        view()->composer('*', function ($view) {
            try {
                $jumlahPengaduanBaru = \App\Models\Pengaduan::where('status', 'baru')->count();
            } catch (\Throwable $e) {
                $jumlahPengaduanBaru = 0;
            }

            $view->with('jumlahPengaduanBaru', $jumlahPengaduanBaru);
        });
    }
}
